<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProducts = Product::where('is_active', true)->count();
        $totalStock = Product::where('is_active', true)->sum('stock');

        $currentMonth = now()->startOfMonth();
        $stockInThisMonth = StockIn::where('transaction_date', '>=', $currentMonth)->count();
        $stockOutThisMonth = StockOut::where('transaction_date', '>=', $currentMonth)->count();

        $lowStockProducts = Product::where('is_active', true)
            ->whereRaw('stock <= minimum_stock')
            ->with(['category', 'location'])
            ->limit(5)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStock',
            'stockInThisMonth',
            'stockOutThisMonth',
            'lowStockProducts',
            'recentActivities'
        ));
    }
}