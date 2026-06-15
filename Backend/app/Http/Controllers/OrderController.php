<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\GetOrderRequest;
use App\Services\Order\GetOrderService;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function getOrder(GetOrderService $service, GetOrderRequest $request): JsonResponse
    {
        return $service->handle($request);
    }
}
