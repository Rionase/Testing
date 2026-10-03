<?php

namespace App\Services\Midtrans;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BaseException;
use App\Exceptions\ValidationException;
use App\Http\Requests\Midtrans\InsertPaymentNotificationRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class InsertPaymentNotificationService
{
    /**
     * Transaction Status Cycle on Midtrans Documentation: https://docs.midtrans.com/docs/transaction-status-cycle
     *
     * @throws Throwable
     */
    public function handle(InsertPaymentNotificationRequest $request): JsonResponse
    {
        $connection = DB::connection('mysql');
        $connection->beginTransaction();

        try {
            $transaction_time = $request->validated('transaction_time');
            $transaction_status = $request->validated('transaction_status');
            $transaction_status = strtoupper($transaction_status);
            $id_order = $request->validated('order_id');
            $expiry_time = $request->validated('expiry_time');

            $order = Order::query()->findOrFail($id_order);

            $beforeStatusPending = $order->id_order_status == OrderStatusEnum::PENDING->id();
            $beforeStatusCapture = $order->id_order_status == OrderStatusEnum::CAPTURE->id();
            $beforeStatusSettlement = $order->id_order_status == OrderStatusEnum::SETTLEMENT->id();
            $beforeStatusDeny = $order->id_order_status == OrderStatusEnum::DENY->id();
            $beforeStatusCancel = $order->id_order_status == OrderStatusEnum::CANCEL->id();
            $beforeStatusExpire = $order->id_order_status == OrderStatusEnum::EXPIRE->id();
            $beforeStatusFailure = $order->id_order_status == OrderStatusEnum::FAILURE->id();
            $beforeStatusAuthorize = $order->id_order_status == OrderStatusEnum::AUTHORIZE->id();

            $afterStatusPending = $transaction_status == OrderStatusEnum::PENDING->label();
            $afterStatusCapture = $transaction_status == OrderStatusEnum::CAPTURE->label();
            $afterStatusSettlement = $transaction_status == OrderStatusEnum::SETTLEMENT->id();
            $afterStatusDeny  = $transaction_status == OrderStatusEnum::DENY->label();
            $afterStatusCancel  = $transaction_status == OrderStatusEnum::CANCEL->label();
            $afterStatusExpire = $transaction_status == OrderStatusEnum::EXPIRE->label();
            $afterStatusFailure = $transaction_status == OrderStatusEnum::FAILURE->label();

            if ($request->has('fraud_status') && $request->validated('fraud_status') != 'accept') {
                throw new ValidationException('Fraud Status must have accept value.');
            }

            if ($beforeStatusPending && $afterStatusPending) {
                // PENDING -> PENDING
                // Perlu update order.expired_at karena setelah memilih metode pembayaran,
                // waktu expired akan berubah menjadi env(MIDTRANS_PAYMENT_EXPIRY_DURATION_MINUTES) menit semenjak metode pembayaran dipilih
                $order->update([ 'expired_at' => $expiry_time ]);

            } else if (($beforeStatusAuthorize && $afterStatusCapture) || ($beforeStatusPending && $afterStatusSettlement)) {
                // PENDING -> SETTLEMENT || AUTHORIZE -> CAPTURE
                // Payment berhasil
                // request.settlement_time tidak digunakan untuk update payment_time,
                // karena settlement_time merupakan waktu ubah ke settlement, sedangkan ada status capture ( sudah diterima bank, tapi belum dilepas ke rekening midtrans )
                $order->update([
                    'id_order_status' => OrderStatusEnum::idFromLabel($transaction_status),
                    'payment_time' => $transaction_time
                ]);

            } else if (
                ($beforeStatusPending && $afterStatusDeny) ||
                ($beforeStatusAuthorize && $afterStatusDeny) ||
                ($beforeStatusPending && $afterStatusCancel) ||
                ($beforeStatusCapture && $afterStatusCancel) ||
                ($beforeStatusAuthorize && $afterStatusCancel) ||
                ($beforeStatusPending && $afterStatusExpire) ||
                ($beforeStatusAuthorize && $afterStatusExpire) ||
                !($beforeStatusDeny || $beforeStatusCancel || $beforeStatusExpire || $beforeStatusFailure) && $afterStatusFailure
            ) {
                // PENDING || AUTHORIZE -> DENY
                // PENDING || CAPTURE || AUTHORIZE -> CANCEL
                // PENDING || AUTHORIZE -> EXPIRE
                // OTHER THAN DENY || CANCEL || EXPIRE || FAILURE -> FAILURE
                // Revert product.quantity dan product.pending_quantity
                $order->update([
                    'id_order_status' => OrderStatusEnum::idFromLabel($transaction_status),
                ]);

                $list_order_details = OrderDetail::query()->where('id_order', $id_order)->get();
                foreach ($list_order_details as $order_detail) {
                    $product = Product::query()->findOrFail($order_detail->id_product);
                    $product->update([
                        'quantity' => $product->quantity + $order_detail->quantity,
                        'pending_quantity' => $product->pending_quantity - $order_detail->quantity
                    ]);
                }

            } else if ($beforeStatusSettlement && $afterStatusDeny) {
                // MIDTRANS DOCUMENTATION: https://docs.midtrans.com/docs/transaction-status-cycle#reversal-case

            } else {
                $order->update([
                    'id_order_status' => OrderStatusEnum::idFromLabel($transaction_status),
                ]);
            }

            $connection->commit();

            return ResponseUtil::success(message: 'berhasil');

        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw new BaseException(message: $throwable->getMessage(), code: $throwable->getCode());
        }
    }
}
