<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'active'])->group(function () {
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Categorías: crear/editar/eliminar solo admin (se registra primero para que
    // /categories/create no sea interpretado como /categories/{category})
    Route::middleware('admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    });

    // Categorías: consulta para todos los usuarios autenticados
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);
});

require __DIR__.'/auth.php';