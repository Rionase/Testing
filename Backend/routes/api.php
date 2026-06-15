<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MitransController;
use App\Http\Controllers\OrderController;

Route::prefix('mitrans')->group(function() {
    Route::post('transaction', [MitransController::class, 'insertTransaction']);
});

Route::prefix('order')->group(function () {
    Route::get('', [OrderController::class, 'getOrder']);
    Route::post('', [OrderController::class, 'insertOrder']);
});
