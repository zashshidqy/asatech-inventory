<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockInController extends Controller
{
    public function create()
    {
        $products = Product::where('is_active', true)
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get();

        $productsData = $products->map(function($p) {
            return [
                'id' => $p->id,
                'code' => $p->code,
                'name' => $p->name,
                'category' => $p->category->name ?? '-',
                'location' => $p->location->name ?? '-',
                'stock' => $p->stock,
                'unit' => $p->unit,
                'purchase_price' => $p->purchase_price,
            ];
        })->values();

        return view('stock-in.create', compact('products', 'productsData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'source' => 'required|string|max:150',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('stock_in_items', 'product_id')->where(function ($query) {
                    // We'll handle uniqueness in the controller since we don't have stock_in_id yet
                }),
            ],
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'nullable|numeric|min:0|max:9999999999.99',
        ]);

        // Check for duplicate products in the request
        $productIds = collect($validated['items'])->pluck('product_id');
        if ($productIds->count() !== $productIds->unique()->count()) {
            return back()->withErrors([
                'items' => 'Produk tidak boleh duplikat dalam satu transaksi.'
            ])->withInput();
        }

        // Verify all products are active
        $productIdsArray = $productIds->toArray();
        $inactiveProducts = Product::whereIn('id', $productIdsArray)
            ->where('is_active', false)
            ->pluck('name')
            ->toArray();

        if (!empty($inactiveProducts)) {
            return back()->withErrors([
                'items' => 'Produk berikut tidak aktif dan tidak dapat dipilih: ' . implode(', ', $inactiveProducts)
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate transaction code
            $transactionCode = $this->generateTransactionCode('IN');

            // Create stock in header
            $stockIn = StockIn::create([
                'transaction_code' => $transactionCode,
                'transaction_date' => $validated['transaction_date'],
                'source' => $validated['source'],
                'notes' => $validated['notes'],
                'user_id' => auth()->id(),
            ]);

            // Process each item with row locking
            foreach ($validated['items'] as $item) {
                // Lock product row for update
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                // Verify product is still active (double check)
                if (!$product->is_active) {
                    throw new \Exception("Produk {$product->name} tidak aktif.");
                }

                // Update stock
                $product->increment('stock', $item['quantity']);

                // Create stock in item
                StockInItem::create([
                    'stock_in_id' => $stockIn->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'] ?? null,
                ]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'stock_in.created',
                'description' => "Barang masuk: {$transactionCode} dari {$validated['source']} ({$stockIn->items->count()} item)",
            ]);

            DB::commit();

            return redirect()->route('stock-in.create')
                ->with('success', "Barang masuk berhasil dicatat. Kode: {$transactionCode}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Gagal mencatat barang masuk: ' . $e->getMessage()
            ])->withInput();
        }
    }

    private function generateTransactionCode(string $prefix): string
    {
        $date = now()->format('Ymd');
        $prefixCode = "{$prefix}-{$date}";

        // Find the last sequence number for today
        $lastTransaction = StockIn::where('transaction_code', 'like', "{$prefixCode}-%")
            ->latest('id')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_code, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return "{$prefixCode}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}