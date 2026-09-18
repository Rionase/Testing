<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderController;

Route::get('landing', [Controller::class, 'getLanding']);

Route::prefix('product')->group(function () {
    Route::get('', [ProductController::class, 'getProduct'])->name('get_product');
    Route::get('{id}', [ProductController::class, 'getProductDetail'])->name('get_product_detail');
});

Route::prefix('midtrans')->group(function() {
    Route::post('payment', [MidtransController::class, 'insertPayment']);
    Route::post('payment-notification', [MidtransController::class, 'insertPaymentNotification']);

    if (env('APP_ENV') == 'local') {
        Route::post('transaction', [MidtransController::class, 'insertTransaction']);
    }
});

Route::prefix('order')->group(function () {
    Route::get('', [OrderController::class, 'getOrder']);
    Route::get('{id}', [OrderController::class, 'getOrderDetail']);
    Route::post('', [OrderController::class, 'insertOrder']);
});
