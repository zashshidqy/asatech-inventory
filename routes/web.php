<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\TransactionHistoryController;
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

    // Transaction History
    Route::get('/transactions', [TransactionHistoryController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/stock-in/{stockIn}', [TransactionHistoryController::class, 'showStockIn'])->name('transactions.show-stock-in');
    Route::get('/transactions/stock-out/{stockOut}', [TransactionHistoryController::class, 'showStockOut'])->name('transactions.show-stock-out');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/current-stock', [ReportController::class, 'currentStock'])->name('reports.current-stock');
    Route::get('/reports/current-stock/export', [ReportController::class, 'exportCurrentStock'])->name('reports.export-current-stock');
    Route::get('/reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');
    Route::get('/reports/stock-movement/export', [ReportController::class, 'exportStockMovement'])->name('reports.export-stock-movement');

    // Placeholder routes for navigation (to be implemented)
    Route::get('/users', function () { return 'Users - Coming Soon'; })->name('users.index');
});