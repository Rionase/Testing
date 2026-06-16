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
            ->join('status_order', 'status_order.id', 'order.id_status_order')
            ->select([
                'order.id AS id_order',
                'order.gross_ammount',
                'order.keterangan',
                'status_order.id AS id_status_order',
                'status_order.nama AS nama_status_order',
            ])
            ->get();

        return ResponseUtil::success(data: $list_order);
    }
}
