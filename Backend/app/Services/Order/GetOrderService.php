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
        $list_order = Order::query()->get();

        return ResponseUtil::success(data: $list_order);
    }
}
