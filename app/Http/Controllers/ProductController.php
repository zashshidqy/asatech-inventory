<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'location'])->withCount('stockInItems');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= minimum_stock');
            } elseif ($request->stock_status === 'normal') {
                $query->whereRaw('stock > minimum_stock');
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSort = ['code', 'name', 'stock', 'minimum_stock', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSort)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest();
        }

        $products = $query->paginate(10)->withQueryString();

        // For filter dropdowns
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'locations'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('products.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code',
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'brand' => 'nullable|string|max:100',
            'item_type' => 'required|in:trade_goods,company_asset,operational_supply',
            'unit' => 'required|string|max:30',
            'purchase_price' => 'nullable|numeric|min:0|max:9999999999.99',
            'selling_price' => 'nullable|numeric|min:0|max:9999999999.99',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Set default values
        $validated['stock'] = 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'location', 'stockInItems.stockIn', 'stockOutItems.stockOut']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'locations'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'brand' => 'nullable|string|max:100',
            'item_type' => 'required|in:trade_goods,company_asset,operational_supply',
            'unit' => 'required|string|max:30',
            'purchase_price' => 'nullable|numeric|min:0|max:9999999999.99',
            'selling_price' => 'nullable|numeric|min:0|max:9999999999.99',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Note: stock field is NOT in validated - cannot be edited directly
        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Check if product has transaction history
        if ($product->stockInItems()->count() > 0 || $product->stockOutItems()->count() > 0) {
            // Soft delete - just deactivate
            $product->update(['is_active' => false]);
            return redirect()->route('products.index')
                ->with('success', 'Produk memiliki riwayat transaksi. Status diubah menjadi nonaktif.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}