<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\UserStatusController;
use App\Http\Controllers\Api\V1\Admin\ProductVariantController;


Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('products.variants', ProductVariantController::class);

Route::post('/product-variants/batch-upsert', [ProductVariantController::class, 'batchUpsert']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('users/{user}/change-status', UserStatusController::class);

    Route::get('orders', [App\Http\Controllers\Api\V1\Admin\OrderController::class, 'index']);
    Route::patch('orders/{order}/status', [App\Http\Controllers\Api\V1\Admin\OrderController::class, 'updateStatus']);
    Route::get('orders/export', [App\Http\Controllers\Api\V1\Admin\OrderController::class, 'exportExcel']);
});
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('users/{user}/change-status', UserStatusController::class);
});
