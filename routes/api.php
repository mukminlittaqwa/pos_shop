<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SuperAdmin\ShopController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CashierController;

Route::post('login', [AuthController::class, 'login'])->name('login');;

Route::middleware('auth:api')->group(function () {
     Route::get('profile', [AuthController::class, 'me']);
     Route::put('profile', [AuthController::class, 'updateProfile']);
     Route::post('logout', [AuthController::class, 'logout']);
     Route::apiResource('shops', ShopController::class)
          ->middleware('role:super-admin');
     Route::apiResource('products', ProductController::class);
     Route::apiResource('sales', SaleController::class)
          ->only(['index', 'store', 'show']);
     Route::apiResource('cashiers', CashierController::class)
          ->middleware('permission:manage_cashiers');
     });