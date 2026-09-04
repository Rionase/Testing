<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\GetProductDetailRequest;
use App\Services\Product\GetProductDetailService;
use App\Services\Product\GetProductService;
use Illuminate\Http\JsonResponse;

class ProductController
{
    public function getProduct(GetProductService $service): JsonResponse
    {
        return $service->handle();
    }

    public function getProductDetail(GetProductDetailService $service, GetProductDetailRequest $request): JsonResponse
    {
        return $service->handle($request);
    }
}
