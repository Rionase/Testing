<?php

namespace App\Services\Product;

use App\Http\Requests\Product\GetProductDetailRequest;
use App\Models\Products;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class GetProductDetailService
{
    public function handle(GetProductDetailRequest $request): JsonResponse
    {
        $id = $request->validated('id');

        $data = Products::query()->select([
            'products.id',
            'products.name',
            'products.description',
            'products.quantity'
        ])->where('id', $id)
        ->first();

        return ResponseUtil::success($data);
    }
}
