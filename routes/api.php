<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryMovementController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['api.token', 'throttle:60,1'])
    ->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show'])->whereNumber('product');
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('movements', [InventoryMovementController::class, 'index']);
    });
