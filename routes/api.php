<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('products/catalog', [ProductController::class, 'catalog']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
    
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('products', ProductController::class);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    });
});