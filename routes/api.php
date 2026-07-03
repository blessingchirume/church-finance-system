<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);

    Route::middleware('mobile.token')->group(function () {
        Route::get('/assemblies', [MobileDataController::class, 'assemblies']);
        Route::get('/chart-accounts', [MobileDataController::class, 'chartAccounts']);
        Route::post('/transactions', [MobileDataController::class, 'storeTransaction']);
        Route::get('/transactions/recent', [MobileDataController::class, 'recentTransactions']);
    });
});
