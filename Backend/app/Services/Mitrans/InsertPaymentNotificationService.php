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

            Order::query()->find($order_id)->update([
                'midtrans_payment_status' => $transaction_status,
                'midtrans_payment_time' => $transaction_time
            ]);

            return ResponseUtil::success(message: 'berhasil');

        } catch (Throwable $throwable) {
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }
    }
}
