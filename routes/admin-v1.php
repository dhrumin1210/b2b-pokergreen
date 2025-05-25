<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\OrderController;
use App\Http\Controllers\Api\V1\Admin\UserStatusController;
use App\Http\Controllers\Api\V1\Admin\ProductVariantController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;

// Admin authentication (login) route can remain public if needed
Route::post('login', [AuthController::class, 'login']);

// All other admin routes require authentication
Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::get('me', [UserController::class, 'me']);
    Route::post('me', [UserController::class, 'updateProfile']);
    Route::apiResource('users', UserController::class)->only(['index', 'show']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('products.variants', ProductVariantController::class);

    Route::post('/product-variants/batch-upsert', [ProductVariantController::class, 'batchUpsert']);

    Route::post('users/{user}/change-status', UserStatusController::class);

    Route::get('orders', [OrderController::class, 'index']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('orders/export', [OrderController::class, 'exportExcel']);
    Route::get('dashboard-stats', [DashboardController::class, 'stats']);
});