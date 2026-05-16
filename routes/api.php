<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransactionDetailsController;
use App\Http\Controllers\PaymentVerificationController;

//Public routes

// AUTH
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/registration', [AuthController::class, 'registration']);
Route::apiResource('roles', RoleController::class);
Route::apiResource('categories', CategoriesController::class);
Route::apiResource('products', ProductsController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('transaction-details', TransactionDetailsController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('paymentverification', PaymentVerificationController::class);
Route::get('/my-orders/{token}', [TransactionController::class, 'myOrders']);
//Protected Routes
Route::middleware('auth:sanctum')->group(function () {

  // Route::apiResource('users', UserController::class);
  // Route::apiResource('roles', RoleController::class);
  // Route::apiResource('categories', CategoriesController::class);
  // Route::apiResource('products', ProductsController::class);
  // Route::apiResource('transactions', TransactionController::class);
  // Route::apiResource('transaction-details', TransactionDetailsController::class);
});
