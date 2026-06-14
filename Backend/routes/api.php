<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MitransController;

Route::prefix('mitrans')->group(function() {
    Route::post('transaction', [MitransController::class, 'insertTransaction']);
});
