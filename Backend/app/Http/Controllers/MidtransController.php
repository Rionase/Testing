<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mitrans\InsertPaymentNotificationRequest;
use App\Services\Mitrans\InsertPaymentNotificationService;
use App\Services\Mitrans\InsertTransactionService;
use App\Http\Requests\Mitrans\InsertTransactionRequest;
use Illuminate\Http\JsonResponse;

class MidtransController
{
    public function insertTransaction(InsertTransactionService $service, InsertTransactionRequest $request): JsonResponse
    {
        return $service->handle($request);
    }

    public function insertPaymentNotification(InsertPaymentNotificationService $service, InsertPaymentNotificationRequest $request): JsonResponse
    {
        return $service->handle($request);
    }
}
