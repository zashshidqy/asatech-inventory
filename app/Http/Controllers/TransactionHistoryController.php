<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, stock_in, stock_out
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $stockInQuery = StockIn::with(['user', 'items.product.category', 'items.product.location'])
            ->latest('transaction_date')
            ->latest('id');

        $stockOutQuery = StockOut::with(['user', 'items.product.category', 'items.product.location'])
            ->latest('transaction_date')
            ->latest('id');

        // Apply filters
        if ($dateFrom) {
            $stockInQuery->where('transaction_date', '>=', $dateFrom);
            $stockOutQuery->where('transaction_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $stockInQuery->where('transaction_date', '<=', $dateTo);
            $stockOutQuery->where('transaction_date', '<=', $dateTo);
        }
        if ($search) {
            $stockInQuery->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
            $stockOutQuery->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = 15;

        if ($type === 'stock_in') {
            $stockIns = $stockInQuery->paginate($perPage)->withQueryString();
            $stockOuts = collect();
        } elseif ($type === 'stock_out') {
            $stockIns = collect();
            $stockOuts = $stockOutQuery->paginate($perPage)->withQueryString();
        } else {
            // Combine both with manual pagination
            $stockIns = $stockInQuery->get();
            $stockOuts = $stockOutQuery->get();

            // Merge and sort by date desc
            $merged = $stockIns->concat($stockOuts)->sortByDesc(function ($item) {
                return $item->transaction_date . ' ' . $item->created_at;
            })->values();

            // Manual pagination
            $page = request()->get('page', 1);
            $stockIns = $merged->forPage($page, $perPage);
            $stockOuts = collect(); // We'll use merged in view
            $mergedPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $stockIns,
                $merged->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        // Stats for summary cards
        $totalIn = StockIn::when($dateFrom, fn($q) => $q->where('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('transaction_date', '<=', $dateTo))
            ->count();
        $totalOut = StockOut::when($dateFrom, fn($q) => $q->where('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('transaction_date', '<=', $dateTo))
            ->count();
        $totalItemsIn = StockIn::when($dateFrom, fn($q) => $q->where('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('transaction_date', '<=', $dateTo))
            ->withCount('items')->get()->sum('items_count');
        $totalItemsOut = StockOut::when($dateFrom, fn($q) => $q->where('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('transaction_date', '<=', $dateTo))
            ->withCount('items')->get()->sum('items_count');

        return view('transactions.index', compact(
            'stockIns', 'stockOuts', 'mergedPaginated',
            'type', 'dateFrom', 'dateTo', 'search',
            'totalIn', 'totalOut', 'totalItemsIn', 'totalItemsOut'
        ));
    }

    public function showStockIn(StockIn $stockIn)
    {
        $stockIn->load(['user', 'items.product.category', 'items.product.location']);
        return view('transactions.show-stock-in', compact('stockIn'));
    }

    public function showStockOut(StockOut $stockOut)
    {
        $stockOut->load(['user', 'items.product.category', 'items.product.location']);
        return view('transactions.show-stock-out', compact('stockOut'));
    }
}