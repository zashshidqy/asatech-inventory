<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\StockOut;
use App\Models\StockOutItem;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'current_stock'); // current_stock, stock_movement

        return view('reports.index', compact('reportType'));
    }

    public function currentStock(Request $request)
    {
        $categoryId = $request->get('category_id');
        $locationId = $request->get('location_id');
        $stockStatus = $request->get('stock_status'); // all, low, out, normal
        $search = $request->get('search');

        $query = Product::with(['category', 'location'])
            ->where('is_active', true)
            ->orderBy('name');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        // Apply stock status filter after getting products (since it's computed)
        if ($stockStatus && $stockStatus !== 'all') {
            $products = $products->filter(function ($product) use ($stockStatus) {
                if ($stockStatus === 'out') return $product->stock === 0;
                if ($stockStatus === 'low') return $product->stock > 0 && $product->stock <= $product->minimum_stock;
                if ($stockStatus === 'normal') return $product->stock > $product->minimum_stock;
                return true;
            })->values();
        }

        // Summary stats
        $totalProducts = $products->count();
        $totalStock = $products->sum('stock');
        $totalValue = $products->sum(function ($p) { return $p->stock * ($p->purchase_price ?? 0); });
        $lowStockCount = $products->filter(fn($p) => $p->stock > 0 && $p->stock <= $p->minimum_stock)->count();
        $outOfStockCount = $products->filter(fn($p) => $p->stock === 0)->count();

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('reports.current-stock', compact(
            'products', 'categories', 'locations',
            'categoryId', 'locationId', 'stockStatus', 'search',
            'totalProducts', 'totalStock', 'totalValue', 'lowStockCount', 'outOfStockCount'
        ));
    }

    public function stockMovement(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id');
        $locationId = $request->get('location_id');
        $productId = $request->get('product_id');
        $type = $request->get('type', 'all'); // all, in, out

        // Get stock in movements
        $stockInQuery = StockInItem::join('stock_ins', 'stock_in_items.stock_in_id', '=', 'stock_ins.id')
            ->join('products', 'stock_in_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('locations', 'products.location_id', '=', 'locations.id')
            ->whereDate('stock_ins.transaction_date', '>=', $dateFrom)
            ->whereDate('stock_ins.transaction_date', '<=', $dateTo)
            ->select(
                'stock_in_items.*',
                'stock_ins.transaction_date',
                'stock_ins.transaction_code',
                'stock_ins.source',
                'products.name as product_name',
                'products.code as product_code',
                'products.unit',
                'categories.name as category_name',
                'locations.name as location_name'
            );

        // Get stock out movements
        $stockOutQuery = StockOutItem::join('stock_outs', 'stock_out_items.stock_out_id', '=', 'stock_outs.id')
            ->join('products', 'stock_out_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('locations', 'products.location_id', '=', 'locations.id')
            ->whereDate('stock_outs.transaction_date', '>=', $dateFrom)
            ->whereDate('stock_outs.transaction_date', '<=', $dateTo)
            ->select(
                'stock_out_items.*',
                'stock_outs.transaction_date',
                'stock_outs.transaction_code',
                'stock_outs.purpose',
                'stock_outs.recipient',
                'products.name as product_name',
                'products.code as product_code',
                'products.unit',
                'categories.name as category_name',
                'locations.name as location_name'
            );

        if ($categoryId) {
            $stockInQuery->where('products.category_id', $categoryId);
            $stockOutQuery->where('products.category_id', $categoryId);
        }
        if ($locationId) {
            $stockInQuery->where('products.location_id', $locationId);
            $stockOutQuery->where('products.location_id', $locationId);
        }
        if ($productId) {
            $stockInQuery->where('stock_in_items.product_id', $productId);
            $stockOutQuery->where('stock_out_items.product_id', $productId);
        }

        $stockIns = collect();
        $stockOuts = collect();

        if ($type === 'all' || $type === 'in') {
            $stockIns = $stockInQuery->orderBy('stock_ins.transaction_date', 'desc')
                ->orderBy('stock_ins.id', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->type = 'in';
                    $item->reference = $item->transaction_code;
                    $item->description = $item->source;
                    return $item;
                });
        }

        if ($type === 'all' || $type === 'out') {
            $stockOuts = $stockOutQuery->orderBy('stock_outs.transaction_date', 'desc')
                ->orderBy('stock_outs.id', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->type = 'out';
                    $item->reference = $item->transaction_code;
                    $item->description = $item->purpose . ($item->recipient ? " - {$item->recipient}" : '');
                    return $item;
                });
        }

        // Merge and sort
        $movements = $stockIns->concat($stockOuts)->sortByDesc(function ($item) {
            return $item->transaction_date . ' ' . $item->id;
        })->values();

        // Summary
        $totalInQty = $stockIns->sum('quantity');
        $totalOutQty = $stockOuts->sum('quantity');
        $totalInValue = $stockIns->sum(function ($item) { return $item->quantity * ($item->purchase_price ?? 0); });
        $uniqueProducts = $movements->pluck('product_id')->unique()->count();
        $totalTransactions = $stockIns->pluck('stock_in_id')->unique()->count() + $stockOuts->pluck('stock_out_id')->unique()->count();

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('reports.stock-movement', compact(
            'movements', 'categories', 'locations', 'products',
            'dateFrom', 'dateTo', 'categoryId', 'locationId', 'productId', 'type',
            'totalInQty', 'totalOutQty', 'totalInValue', 'uniqueProducts', 'totalTransactions'
        ));
    }

    public function exportCurrentStock(Request $request)
    {
        // Same query as currentStock but return CSV
        $categoryId = $request->get('category_id');
        $locationId = $request->get('location_id');
        $stockStatus = $request->get('stock_status');
        $search = $request->get('search');

        $query = Product::with(['category', 'location'])
            ->where('is_active', true)
            ->orderBy('name');

        if ($categoryId) $query->where('category_id', $categoryId);
        if ($locationId) $query->where('location_id', $locationId);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        if ($stockStatus && $stockStatus !== 'all') {
            $products = $products->filter(function ($product) use ($stockStatus) {
                if ($stockStatus === 'out') return $product->stock === 0;
                if ($stockStatus === 'low') return $product->stock > 0 && $product->stock <= $product->minimum_stock;
                if ($stockStatus === 'normal') return $product->stock > $product->minimum_stock;
                return true;
            })->values();
        }

        $filename = 'stok_saat_ini_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'Kode Produk', 'Nama Produk', 'Kategori', 'Lokasi', 'Satuan',
                'Stok Tersedia', 'Stok Minimum', 'Status Stok',
                'Harga Beli', 'Harga Jual', 'Nilai Stok (Harga Beli)'
            ]);

            foreach ($products as $product) {
                $stock = $product->stock;
                $minStock = $product->minimum_stock;
                
                if ($stock === 0) {
                    $status = 'HABIS';
                } elseif ($stock <= $minStock) {
                    $status = 'RENDAH';
                } else {
                    $status = 'NORMAL';
                }

                fputcsv($file, [
                    $product->code,
                    $product->name,
                    $product->category->name ?? '-',
                    $product->location->name ?? '-',
                    $product->unit,
                    $stock,
                    $minStock,
                    $status,
                    $product->purchase_price ? number_format($product->purchase_price, 0, ',', '.') : '-',
                    $product->selling_price ? number_format($product->selling_price, 0, ',', '.') : '-',
                    $product->purchase_price ? number_format($stock * $product->purchase_price, 0, ',', '.') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportStockMovement(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id');
        $locationId = $request->get('location_id');
        $productId = $request->get('product_id');
        $type = $request->get('type', 'all');

        $stockInQuery = StockInItem::join('stock_ins', 'stock_in_items.stock_in_id', '=', 'stock_ins.id')
            ->join('products', 'stock_in_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('locations', 'products.location_id', '=', 'locations.id')
            ->whereDate('stock_ins.transaction_date', '>=', $dateFrom)
            ->whereDate('stock_ins.transaction_date', '<=', $dateTo)
            ->select(
                'stock_in_items.*',
                'stock_ins.transaction_date',
                'stock_ins.transaction_code',
                'stock_ins.source',
                'products.name as product_name',
                'products.code as product_code',
                'products.unit',
                'categories.name as category_name',
                'locations.name as location_name'
            );

        $stockOutQuery = StockOutItem::join('stock_outs', 'stock_out_items.stock_out_id', '=', 'stock_outs.id')
            ->join('products', 'stock_out_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('locations', 'products.location_id', '=', 'locations.id')
            ->whereDate('stock_outs.transaction_date', '>=', $dateFrom)
            ->whereDate('stock_outs.transaction_date', '<=', $dateTo)
            ->select(
                'stock_out_items.*',
                'stock_outs.transaction_date',
                'stock_outs.transaction_code',
                'stock_outs.purpose',
                'stock_outs.recipient',
                'products.name as product_name',
                'products.code as product_code',
                'products.unit',
                'categories.name as category_name',
                'locations.name as location_name'
            );

        if ($categoryId) {
            $stockInQuery->where('products.category_id', $categoryId);
            $stockOutQuery->where('products.category_id', $categoryId);
        }
        if ($locationId) {
            $stockInQuery->where('products.location_id', $locationId);
            $stockOutQuery->where('products.location_id', $locationId);
        }
        if ($productId) {
            $stockInQuery->where('stock_in_items.product_id', $productId);
            $stockOutQuery->where('stock_out_items.product_id', $productId);
        }

        $stockIns = collect();
        $stockOuts = collect();

        if ($type === 'all' || $type === 'in') {
            $stockIns = $stockInQuery->orderBy('stock_ins.transaction_date', 'desc')
                ->orderBy('stock_ins.id', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->type = 'in';
                    $item->reference = $item->transaction_code;
                    $item->description = $item->source;
                    return $item;
                });
        }

        if ($type === 'all' || $type === 'out') {
            $stockOuts = $stockOutQuery->orderBy('stock_outs.transaction_date', 'desc')
                ->orderBy('stock_outs.id', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->type = 'out';
                    $item->reference = $item->transaction_code;
                    $item->description = $item->purpose . ($item->recipient ? " - {$item->recipient}" : '');
                    return $item;
                });
        }

        $movements = $stockIns->concat($stockOuts)->sortByDesc(function ($item) {
            return $item->transaction_date . ' ' . $item->id;
        })->values();

        $filename = 'pergerakan_stok_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($movements) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Tanggal', 'Kode Transaksi', 'Jenis', 'Kode Produk', 'Nama Produk',
                'Kategori', 'Lokasi', 'Satuan', 'Kuantitas',
                'Keterangan', 'Harga Beli', 'Nilai (Harga Beli x Qty)'
            ]);

            foreach ($movements as $item) {
                $jenis = $item->type === 'in' ? 'MASUK' : 'KELUAR';
                fputcsv($file, [
                    \Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y'),
                    $item->reference,
                    $jenis,
                    $item->product_code,
                    $item->product_name,
                    $item->category_name ?? '-',
                    $item->location_name ?? '-',
                    $item->unit,
                    $item->quantity,
                    $item->description,
                    $item->purchase_price ? number_format($item->purchase_price, 0, ',', '.') : '-',
                    $item->purchase_price ? number_format($item->quantity * $item->purchase_price, 0, ',', '.') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}