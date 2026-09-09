<?php

namespace App\Services\Order;

use App\Exceptions\BaseException;
use App\Exceptions\ValidationException;
use App\Http\Requests\Order\InsertOrderRequest;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Products;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class InsertOrderService
{
    /**
     * @throws Throwable
     */
    public function handle(InsertOrderRequest $request): JsonResponse
    {
        $midtrans_expiry_duration_minutes = (int) env('MIDTRANS_EXPIRY_DURATION_MINUTES');

        $connection = DB::connection('mysql');
        $connection->beginTransaction();

        try {
            $customer_name = $request->validated('customer_name');
            $customer_email = $request->validated('customer_email');
            $customer_phone = $request->validated('customer_phone');
            $notes = $request->validated('notes');
            $list_products = $request->validated('list_products');

            $orders = Orders::query()->create([
                'id_order_status' => 1, // INITIATED
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'notes' => $notes,
                'total_price' => 0,
                'snap_token' => null,
                'snap_redirect_url' => null,
                'expired_at' => now()->addMinutes($midtrans_expiry_duration_minutes),
                'payment_time' => null
            ]);
            $total_price = 0;

            foreach ($list_products as $product) {
                $id_product = $product['id_product'];
                $quantity = $product['quantity'];

                $product = Products::query()->findOrFail($id_product);

                if ($quantity > $product->quantity) {
                    throw new ValidationException("Quantity product : $product->name exced inventory quantity.");
                }

                $product->update([
                    'quantity' => $product->quantity - $quantity
                ]);

                OrderDetails::query()->create([
                    'id_orders' => $orders->id,
                    'id_products' => $id_product,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
                $total_price += $product->price * $quantity;
            }

            $orders->update([
                'total_price' => $total_price
            ]);

            $connection->commit();

            return ResponseUtil::success(
                data: [ 'id_orders' => $orders->id ],
                message: 'Berhasil membuat order.'
            );

        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }


    }
}
