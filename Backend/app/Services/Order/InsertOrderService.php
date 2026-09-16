<?php

namespace App\Services\Order;

use App\Exceptions\BaseException;
use App\Exceptions\ValidationException;
use App\Http\Requests\Order\InsertOrderRequest;
use App\Models\OrderDetails;
use App\Models\Order;
use App\Models\Product;
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
            $list_product = $request->validated('list_product');

            $order = Order::query()->create([
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

            foreach ($list_product as $product) {
                $id_product = $product['id_product'];
                $quantity = $product['quantity'];

                $product = Product::query()->findOrFail($id_product);

                if ($quantity > $product->quantity) {
                    throw new ValidationException("Quantity product : $product->name exced inventory quantity.");
                }

                $product->update([
                    'quantity' => $product->quantity - $quantity
                ]);

                OrderDetails::query()->create([
                    'id_order' => $order->id,
                    'id_product' => $id_product,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
                $total_price += $product->price * $quantity;
            }

            $order->update([
                'total_price' => $total_price
            ]);

            $connection->commit();

            return ResponseUtil::success(
                data: [ 'id_order' => $order->id ],
                message: 'Succesfully create new order.'
            );

        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }


    }
}
