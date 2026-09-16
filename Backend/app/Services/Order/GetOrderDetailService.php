<?php

namespace App\Services\Order;

use App\Http\Requests\Order\GetOrderDetailRequest;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class GetOrderDetailService
{
    public function handle(GetOrderDetailRequest $request): JsonResponse
    {
        $id = $request->validated('id');

        $order = Order::query()->select([
                'order.id',
                'order.id_order_status',
                'order_status.name AS order_status_name',
                'order.customer_name',
                'order.customer_email',
                'order.customer_phone',
                'order.notes',
                'order.total_price',
                'order.expired_at',
                'order.payment_time',
                'order.created_at'
            ])->join('order_status', 'order_status.id', 'order.id_order_status')
            ->where('order.id', $id)
            ->first()
            ->toArray();

        $order_detail = OrderDetails::query()->select([
                'order_detail.id',
                'order_detail.id_product',
                'order_detail.name',
                'product.description',
                'order_detail.quantity',
                'order_detail.price',
            ])->where('order_detail.id_order', $id)
            ->join('product', 'product.id', 'order_detail.id_product')
            ->get()
            ->toArray();

        $result = [
            ...$order,
            'list_order_detail' => $order_detail,
        ];

        return ResponseUtil::success(data: $result);
    }
}
