<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOut;
use App\Models\StockOutItem;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockOutController extends Controller
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
            ];
        })->values();

        return view('stock-out.create', compact('products', 'productsData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'purpose' => 'required|string|max:100',
            'recipient' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],
            'items.*.quantity' => 'required|integer|min:1',
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
            $transactionCode = $this->generateTransactionCode('OUT');

            // Create stock out header
            $stockOut = StockOut::create([
                'transaction_code' => $transactionCode,
                'transaction_date' => $validated['transaction_date'],
                'purpose' => $validated['purpose'],
                'recipient' => $validated['recipient'],
                'notes' => $validated['notes'],
                'user_id' => auth()->id(),
            ]);

            // Process each item with row locking (sorted by product ID to prevent deadlock)
            $sortedItems = collect($validated['items'])->sortBy('product_id')->values();

            foreach ($sortedItems as $item) {
                // Lock product row for update (consistent ordering prevents deadlock)
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                // Verify product is still active
                if (!$product->is_active) {
                    throw new \Exception("Produk {$product->name} tidak aktif.");
                }

                // Check stock availability
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock} {$product->unit}, Diminta: {$item['quantity']} {$product->unit}");
                }

                // Update stock (decrement)
                $product->decrement('stock', $item['quantity']);

                // Create stock out item
                StockOutItem::create([
                    'stock_out_id' => $stockOut->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'stock_out.created',
                'description' => "Barang keluar: {$transactionCode} untuk {$validated['purpose']} ({$stockOut->items->count()} item)",
            ]);

            DB::commit();

            return redirect()->route('stock-out.create')
                ->with('success', "Barang keluar berhasil dicatat. Kode: {$transactionCode}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Gagal mencatat barang keluar: ' . $e->getMessage()
            ])->withInput();
        }
    }

    private function generateTransactionCode(string $prefix): string
    {
        $date = now()->format('Ymd');
        $prefixCode = "{$prefix}-{$date}";

        // Find the last sequence number for today
        $modelClass = $prefix === 'IN' ? StockIn::class : StockOut::class;
        $lastTransaction = $modelClass::where('transaction_code', 'like', "{$prefixCode}-%")
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