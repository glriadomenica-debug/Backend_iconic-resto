<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('user', [UserController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {});
