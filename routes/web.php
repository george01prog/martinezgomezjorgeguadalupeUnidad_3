<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'active'])->group(function () {
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Crear/editar/eliminar: solo admin (se registra primero para que
    // /products/create no sea interpretado como /products/{product})
    Route::middleware('admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
        Route::resource('products', ProductController::class)->except(['index', 'show']);
    });

    // Consulta: todos los usuarios autenticados
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);
    Route::resource('products', ProductController::class)->only(['index', 'show']);
});

require __DIR__.'/auth.php';