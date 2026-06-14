<?php

namespace App\Http\Controllers;

use App\Services\Mitrans\InsertTransactionService;
use App\Http\Requests\Mitrans\InsertTransactionRequest;
use Illuminate\Http\JsonResponse;

class MitransController
{
    public function insertTransaction(InsertTransactionService $service, InsertTransactionRequest $request): JsonResponse
    {
        return $service->handle($request);
    }
}
