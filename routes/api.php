<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransactionDetailsController;
use App\Http\Controllers\Api\PaymentVerificationController;
use App\Http\Controllers\Api\StaffController;

//Public
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('transactions', [TransactionController::class, 'store']);
Route::get('/my-orders/{token}', [TransactionController::class, 'myOrders']);
Route::get('products', [ProductsController::class, 'index']);
Route::get('products/{product}', [ProductsController::class, 'show']);

//Private (access by role)
Route::middleware(['auth:sanctum'])->group(function () {
  Route::middleware(['role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('categories', CategoriesController::class);
    Route::apiResource('staff', StaffController::class);
    Route::apiResource('transaction-details', TransactionDetailsController::class);
    Route::post('products', [ProductsController::class, 'store']);
  });

  Route::middleware(['role:admin,kasir'])->group(function () {

    // Route::apiResource('transactions', TransactionController::class);

    // Payment verification
    Route::post(
      '/payment-verifications/{transactionId}',
      [PaymentVerificationController::class, 'store']
    );

    Route::get(
      '/payment-verifications/{transactionId}',
      [PaymentVerificationController::class, 'show']
    );
  });

  Route::middleware(['role:admin,kasir,dapur'])->group(function () {

    Route::get(
      '/kitchen/orders',
      [TransactionController::class, 'kitchenOrders']
    );
    Route::apiResource('transactions', TransactionController::class);
  });
});
