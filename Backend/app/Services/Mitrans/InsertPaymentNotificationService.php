<?php

namespace App\Services\Mitrans;

use App\Exceptions\BaseException;
use App\Http\Requests\Mitrans\InsertPaymentNotificationRequest;
use App\Http\Requests\Mitrans\InsertTransactionRequest;
use App\Models\Order;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class InsertPaymentNotificationService
{
    /**
     * @throws BaseException
     */
    public function handle(InsertPaymentNotificationRequest $request): JsonResponse
    {
        try {
            $order_id = $request->validated('order_id');
            $transaction_status = $request->validated('transaction_status');
            $transaction_time = $request->validated('transaction_time');
            $expiry_time = $request->validated('expiry_time');

            $order = Order::query()->find($order_id);

            if (is_null($order->status) && $transaction_status == 'pending') {
                // pertama kali snap created
                $order->update([
                    'status' => $transaction_status,
                    'expired_at' => $expiry_time,
                    'snap_created_at' => $transaction_time
                ]);

            } else if ($order->status == 'pending' && $transaction_status == 'pending') {
                // user memilih tipe pembayaran
                $order->update([
                    'expired_at' => $expiry_time,
                ]);

            } else if ($transaction_status == 'settlement' || $transaction_status == 'capture') {
                // pembayaran berhasil
                $order->update([
                    'status' => $transaction_status,
                    'payment_time' => $transaction_time,
                ]);

            } else {
                $order->update([
                    'status' => $transaction_status,
                ]);

            }

            return ResponseUtil::success(message: 'berhasil');

        } catch (Throwable $throwable) {
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }
    }
}
