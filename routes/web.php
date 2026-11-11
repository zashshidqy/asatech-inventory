<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Placeholder routes for navigation (to be implemented in Phase 2)
    Route::get('/products', function () { return 'Products - Coming Soon'; })->name('products.index');
    Route::get('/categories', function () { return 'Categories - Coming Soon'; })->name('categories.index');
    Route::get('/locations', function () { return 'Locations - Coming Soon'; })->name('locations.index');
    Route::get('/stock-in/create', function () { return 'Stock In - Coming Soon'; })->name('stock-in.create');
    Route::get('/stock-out/create', function () { return 'Stock Out - Coming Soon'; })->name('stock-out.create');
    Route::get('/transactions', function () { return 'Transactions - Coming Soon'; })->name('transactions.index');
    Route::get('/reports', function () { return 'Reports - Coming Soon'; })->name('reports.index');
    Route::get('/users', function () { return 'Users - Coming Soon'; })->name('users.index');
    Route::get('/activity-logs', function () { return 'Activity Logs - Coming Soon'; })->name('activity-logs.index');
});