<?php

namespace App\Services\Product;

use App\Models\Products;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class GetProductService
{
    public function handle(): JsonResponse
    {
        $list_products = Products::query()->select([
            'products.id',
            'products.nama',
            'products.keterangan',
            'products.quantity'
        ])->get();

        return ResponseUtil::success($list_products);
    }
}
