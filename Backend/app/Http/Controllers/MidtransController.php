<?php

namespace App\Http\Controllers;

use App\Http\Requests\Midtrans\InsertPaymentNotificationRequest;
use App\Http\Requests\Midtrans\GetPaymentRequest;
use App\Http\Requests\Midtrans\InsertTransactionRequest;
use App\Services\Midtrans\InsertPaymentNotificationService;
use App\Services\Midtrans\InsertTransactionService;
use App\Services\Midtrans\GetPaymentService;
use Illuminate\Http\JsonResponse;

class MidtransController
{
    public function insertTransaction(InsertTransactionService $service, InsertTransactionRequest $request): JsonResponse
    {
        return $service->handle($request);
    }

    public function getPayment(GetPaymentService $service, GetPaymentRequest $request): JsonResponse
    {
        return $service->handle($request);
    }

    public function insertPaymentNotification(InsertPaymentNotificationService $service, InsertPaymentNotificationRequest $request): JsonResponse
    {
        return $service->handle($request);
    }
}
