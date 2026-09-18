<?php

namespace App\Services\Midtrans;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BaseException;
use App\Exceptions\ValidationException;
use App\Http\Requests\Midtrans\InsertPaymentRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Utils\MidtransUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class InsertPaymentService
{
    /**
     * @throws Throwable
     */
    public function handle(InsertPaymentRequest $request): JsonResponse
    {
        $id_order = $request->validated('id_order');

        $connection = DB::connection('mysql');
        $connection->beginTransaction();

        try {

            $order = Order::query()->where('id', $id_order)->firstOrFail();
            $order_status_name = $order->orderStatus->name;

            $now = now();
            if ($now > $order->expired_at) {
                throw new ValidationException('Order has expired.');

            } else if ($order_status_name == OrderStatusEnum::INITIATED->value) {
                // select perlu sesuai dengan format INSERT MIDTRANS TRANSACTION ['item_details']
                $list_order_detail = OrderDetail::query()->select([
                    'product.id AS id',
                    'product.name AS name',
                    'product.price AS price',
                    'order_detail.quantity AS quantity',
                ])->join('product', 'product.id', 'order_detail.id_product')
                    ->where('id_order', $id_order)
                    ->get()
                    ->toArray();

                $remaining_minutes_page_expiry = (int) ceil($now->diffInMinutes($order->expired_at));
                $midtrans_payment_expiry_duration_minutes = (int) env('MIDTRANS_PAYMENT_EXPIRY_DURATION_MINUTES');

                $midtrans_transaction_payload = [
                    'transaction_details' => [
                        'order_id' => $order->id,
                        'gross_amount' => $order->total_price,
                    ],
                    'item_details' => $list_order_detail,
                    'customer_details' => [
                        'first_name' => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone,
                    ],
                    'page_expiry' => [
                        'unit' => 'minutes',
                        'duration' => $remaining_minutes_page_expiry,
                    ],
                    'expiry' => [
                        'unit' => 'minutes',
                        'duration' => $midtrans_payment_expiry_duration_minutes,
                    ]
                ];

                $response = MidtransUtil::insertMidtransTransaction($midtrans_transaction_payload);
                $snap_token = $response['token'];
                $snap_redirect_url = $response['redirect_url'];

                $order->update([
                    'id_order_status' => 2, // PENDING
                    'snap_token' => $snap_token,
                    'snap_redirect_url' => $snap_redirect_url,
                ]);

                $connection->commit();

            } else if ($order->orderStatus->name == OrderStatusEnum::PENDING->value) {
                $snap_token = $order->snap_token;
                $snap_redirect_url = $order->snap_redirect_url;

            } else {
                throw new ValidationException("Unable to do payment on $order_status_name status.");
            }

            return ResponseUtil::success(data: [
                'snap_token' => $snap_token,
                'snap_redirect_url' => $snap_redirect_url,
            ]);

        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }
    }
}
