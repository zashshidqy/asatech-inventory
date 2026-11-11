<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Locations
    Route::resource('locations', LocationController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Stock In
    Route::get('/stock-in/create', [StockInController::class, 'create'])->name('stock-in.create');
    Route::post('/stock-in', [StockInController::class, 'store'])->name('stock-in.store');

    // Stock Out
    Route::get('/stock-out/create', [StockOutController::class, 'create'])->name('stock-out.create');
    Route::post('/stock-out', [StockOutController::class, 'store'])->name('stock-out.store');

    // Placeholder routes for navigation (to be implemented)
    Route::get('/transactions', function () { return 'Transactions - Coming Soon'; })->name('transactions.index');
    Route::get('/reports', function () { return 'Reports - Coming Soon'; })->name('reports.index');
    Route::get('/users', function () { return 'Users - Coming Soon'; })->name('users.index');
    Route::get('/activity-logs', function () { return 'Activity Logs - Coming Soon'; })->name('activity-logs.index');
});