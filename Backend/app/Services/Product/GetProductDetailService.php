<?php

namespace App\Services\Product;

use App\Http\Requests\Product\GetProductDetailRequest;
use App\Models\Product;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class GetProductDetailService
{
    public function handle(GetProductDetailRequest $request): JsonResponse
    {
        $id = $request->validated('id');

        $data = Product::query()->select([
            'product.id',
            'product.name',
            'product.description',
            'product.quantity'
        ])->where('id', $id)
        ->first();

        return ResponseUtil::success($data);
    }
}
