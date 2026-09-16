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
        $list_order = Order::query()->select([
                'order.id',
                'order.id_order_status',
                'order_status.name AS order_status_name',
                'order.customer_name',
                'order.customer_email',
                'order.customer_phone',
                'order.total_price',
                'order.expired_at',
                'order.created_at'
            ])->join('order_status', 'order_status.id', 'order.id_order_status')
            ->get();

        return ResponseUtil::success(data: $list_order);
    }
}
