<?php

use App\Http\Controllers\POSController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected POS Routes
Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::get('/', [POSController::class, 'index'])->name('dashboard');
    Route::get('/pos', [POSController::class, 'pos'])->name('pos');
    Route::post('/checkout', [POSController::class, 'checkout'])->name('checkout');

    // Product Management Routes
    Route::get('/products', [POSController::class, 'products'])->name('products');
    Route::post('/products', [POSController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [POSController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [POSController::class, 'deleteProduct'])->name('products.delete');

    // Stock level routes
    Route::get('/stok', [POSController::class, 'stok'])->name('stok');

    // Transaction History Routes
    Route::get('/transactions', [POSController::class, 'transactions'])->name('transactions');

    // Reports & Analytics Routes
    Route::get('/reports', [POSController::class, 'reports'])->name('reports');
    Route::get('/reports/export/excel', [POSController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export/pdf', [POSController::class, 'exportPdf'])->name('reports.pdf');

    // AI Monitoring Routes
    Route::get('/ai', [POSController::class, 'aiMonitor'])->name('ai');
    Route::post('/ai/log', [POSController::class, 'logAiDetection'])->name('ai.log');

    // Settings Routes
    Route::get('/settings', [POSController::class, 'settings'])->name('settings');
    Route::post('/settings', [POSController::class, 'saveSettings'])->name('settings.save');

    // User Management Routes
    Route::get('/users', [POSController::class, 'users'])->name('users');
    Route::post('/users', [POSController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [POSController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [POSController::class, 'deleteUser'])->name('users.delete');
});

