<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExternalProductLookupController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Crear/editar/eliminar: solo admin (se registra primero para que
    // /products/create no sea interpretado como /products/{product})
    Route::middleware('admin')->group(function () {
        Route::get('products/lookup/{barcode}', ExternalProductLookupController::class)
            ->where('barcode', '[0-9]{8,14}')
            ->middleware('throttle:10,1')
            ->name('products.lookup');

        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
        Route::resource('products', ProductController::class)->except(['index', 'show']);

        // Usuarios: módulo exclusivo del administrador (sin show ni destroy)
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    });

    // Consulta: todos los usuarios autenticados
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);
    Route::resource('products', ProductController::class)->only(['index', 'show']);

    // Movimientos: todos los usuarios autenticados pueden consultar y registrar.
    // No hay edición ni eliminación para conservar la trazabilidad.
    Route::resource('movements', InventoryMovementController::class)
        ->only(['index', 'create', 'store', 'show']);

    // Reportes: consulta y exportación para todos los usuarios autenticados
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/{format}', [ReportController::class, 'export'])
        ->whereIn('format', ['csv', 'pdf'])
        ->name('reports.export');
});

require __DIR__.'/auth.php';