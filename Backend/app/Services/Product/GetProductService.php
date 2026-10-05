<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class GetProductService
{
    public function handle(): JsonResponse
    {
        $list_product = Product::query()->select([
            'product.id',
            'product.name',
            'product.description',
            'product.quantity'
        ])->get();

        return ResponseUtil::success($list_product);
    }
}
