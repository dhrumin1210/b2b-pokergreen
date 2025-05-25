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

    // IMPORTANT: The export route must be defined BEFORE the general orders resource route
    Route::get('orders/export', [OrderController::class, 'exportExcel']);
    
    // Then define the resource routes for orders
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('dashboard-stats', [DashboardController::class, 'stats']);


});

    // Clear optimization cache
    Route::get('optimize/clear', function () {
        \Artisan::call('optimize:clear');
        return response()->json(['message' => 'Cache cleared successfully']);
    });

    // Clear migration cache
    Route::get('migrate/clear', function () {
        \Artisan::call('migrate');
        return response()->json(['message' => 'Migration cache cleared successfully']);
    });