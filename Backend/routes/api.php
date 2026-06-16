<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderController;

Route::get('landing', [Controller::class, 'getLanding']);

Route::prefix('midtrans')->group(function() {
    Route::post('transaction', [MidtransController::class, 'insertTransaction']);
    Route::post('payment-notification', [MidtransController::class, 'insertPaymentNotification']);
});

Route::prefix('order')->group(function () {
    Route::get('', [OrderController::class, 'getOrder']);
    Route::post('', [OrderController::class, 'insertOrder']);
    Route::post('payment', [OrderController::class, 'insertPayment']);
});
