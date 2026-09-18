<?php

namespace App\Http\Controllers;

use App\Http\Requests\Midtrans\InsertPaymentNotificationRequest;
use App\Http\Requests\Midtrans\InsertPaymentRequest;
use App\Http\Requests\Midtrans\InsertTransactionRequest;
use App\Services\Midtrans\InsertPaymentNotificationService;
use App\Services\Midtrans\InsertTransactionService;
use App\Services\Midtrans\InsertPaymentService;
use Illuminate\Http\JsonResponse;

class MidtransController
{
    public function insertTransaction(InsertTransactionService $service, InsertTransactionRequest $request): JsonResponse
    {
        return $service->handle($request);
    }

    public function insertPayment(InsertPaymentService $service, InsertPaymentRequest $request): JsonResponse
    {
        return $service->handle($request);
    }

    public function insertPaymentNotification(InsertPaymentNotificationService $service, InsertPaymentNotificationRequest $request): JsonResponse
    {
        return $service->handle($request);
    }
}
