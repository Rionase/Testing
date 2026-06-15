<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\GetOrderRequest;
use App\Http\Requests\Order\InsertOrderRequest;
use App\Services\Order\GetOrderService;
use App\Services\Order\InsertOrderService;
use Illuminate\Http\JsonResponse;

class OrderController
{
    public function getOrder(GetOrderService $service, GetOrderRequest $request): JsonResponse
    {
        return $service->handle($request);
    }

    public function insertOrder(InsertOrderService $service, InsertOrderRequest $request): JsonResponse
    {
        return $service->handle($request);
    }
}
