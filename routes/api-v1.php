<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\SignedUrlController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\MasterSettingController;

// Public routes
Route::post('signup', [AuthController::class, 'signUp']);
Route::post('login', [AuthController::class, 'login']);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('forget-password-otp', [AuthController::class, 'forgetPasswordOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('forget-password', [AuthController::class, 'forgetPassword']);
Route::post('reset-password-otp', [AuthController::class, 'resetPasswordOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

// Public resources
Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);
Route::apiResource('settings', MasterSettingController::class)->only(['index', 'show']);
Route::get('countries', CountryController::class);
Route::post('generate-signed-url', SignedUrlController::class);

// Public product routes
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('me', [UserController::class, 'me']);
    Route::post('me', [UserController::class, 'updateProfile']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Order routes
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
    Route::get('orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);
});