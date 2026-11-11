@extends('layouts.app')

@section('title', 'Laporan Stok Saat Ini')

@section('content')
<div class="max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Laporan
        </a>
        <a href="{{ route('reports.export-current-stock', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export CSV
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Stok Saat Ini</h2>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-700">Total Produk</p>
                <p class="text-2xl font-bold text-blue-900">{{ $totalProducts }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-700">Total Unit Stok</p>
                <p class="text-2xl font-bold text-green-900">{{ number_format($totalStock) }}</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <p class="text-sm text-purple-700">Nilai Stok (Harga Beli)</p>
                <p class="text-2xl font-bold text-purple-900">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-700">Stok Rendah</p>
                <p class="text-2xl font-bold text-yellow-900">{{ $lowStockCount }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-700">Stok Habis</p>
                <p class="text-2xl font-bold text-red-900">{{ $outOfStockCount }}</p>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="location_id" id="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ $locationId == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">Status Stok</label>
                    <select name="stock_status" id="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $stockStatus === 'all' || !$stockStatus ? 'selected' : '' }}>Semua</option>
                        <option value="normal" {{ $stockStatus === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="low" {{ $stockStatus === 'low' ? 'selected' : '' }}>Rendah (≤ Min)</option>
                        <option value="out" {{ $stockStatus === 'out' ? 'selected' : '' }}>Habis (0)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Kode atau nama produk..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                        @if($categoryId || $locationId || $stockStatus || $search)
                            <a href="{{ route('reports.current-stock') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            @if($products->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Satuan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Stok</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Min Stok</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Harga Beli</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Harga Jual</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-36">Nilai Stok</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $index => $product)
                            @php
                                $stock = $product->stock;
                                $minStock = $product->minimum_stock;
                                $stockValue = $stock * ($product->purchase_price ?? 0);
                                
                                if ($stock === 0) {
                                    $statusClass = 'bg-red-100 text-red-800';
                                    $statusLabel = 'HABIS';
                                } elseif ($stock <= $minStock) {
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    $statusLabel = 'RENDAH';
                                } else {
                                    $statusClass = 'bg-green-100 text-green-800';
                                    $statusLabel = 'NORMAL';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 @if($stock === 0) bg-red-50 @elseif($stock <= $minStock) bg-yellow-50 @endif">
                                <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ $product->code }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $product->location->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $product->unit }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ number_format($stock) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">{{ number_format($minStock) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">
                                    @if($product->purchase_price)
                                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">
                                    @if($product->selling_price)
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                    @if($product->purchase_price)
                                        Rp {{ number_format($stockValue, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada produk</h3>
                    <p class="mt-1 text-sm text-gray-500">Tidak ada produk yang sesuai dengan filter.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection