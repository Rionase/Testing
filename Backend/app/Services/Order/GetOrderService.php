<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Order\GetOrderRequest;

class GetOrderService
{
    public function handle(GetOrderRequest $request): JsonResponse
    {
        $list_order = Order::query()
            ->select([
                'order.id AS id_order',
                'order.gross_ammount',
                'order.keterangan',
                'order.midtrans_payment_status'
            ])
            ->get();

        return ResponseUtil::success(data: $list_order);
    }
}
