<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

Route::get('/login', [LoginController::class, 'show'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS Terminal
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::post('/switch-store', [PosController::class, 'switchStore'])->name('switch-store')->middleware('can:admin');

    // Products
    Route::resource('products', ProductController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Sales
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

    // Reports (admin)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/api/reports/sales-over-time', [ReportController::class, 'salesOverTime']);
    Route::get('/api/reports/sales-by-user', [ReportController::class, 'salesByUser']);

    // Users (admin)
    Route::resource('users', UserController::class)->middleware('can:admin');

    // Stores (admin)
    Route::resource('stores', StoreController::class)->middleware('can:admin');
});
