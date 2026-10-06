<?php

namespace App\Services\Midtrans;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BaseException;
use App\Exceptions\ValidationException;
use App\Http\Requests\Midtrans\GetPaymentRequest;
use App\Models\Order;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class GetPaymentService
{
    /**
     * @throws Throwable
     */
    public function handle(GetPaymentRequest $request): JsonResponse
    {
        $id_order = $request->validated('id_order');

        $connection = DB::connection('mysql');
        $connection->beginTransaction();

        try {

            $order = Order::query()->where('id', $id_order)->firstOrFail();
            $id_order_status = $order->orderStatus->id;
            $name_order_status = $order->orderStatus->name;

            $now = now();
            if (!is_null($order->payment_time)) {
                throw new ValidationException('Order has been paid.');

            } else if ($now > $order->expired_at) {
                throw new ValidationException('Order has expired.');

            } else if ($id_order_status == OrderStatusEnum::INITIATED->id() || $id_order_status == OrderStatusEnum::PENDING->id()) {
                $snap_token = $order->snap_token;
                $snap_redirect_url = $order->snap_redirect_url;

            } else {
                throw new ValidationException("Unable to do payment on $name_order_status status.");
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
