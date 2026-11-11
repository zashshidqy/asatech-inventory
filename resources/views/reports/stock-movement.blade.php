@extends('layouts.app')

@section('title', 'Laporan Pergerakan Stok')

@section('content')
<div class="max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Laporan
        </a>
        <a href="{{ route('reports.export-stock-movement', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export CSV
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Pergerakan Stok</h2>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-700">Total Barang Masuk</p>
                <p class="text-2xl font-bold text-blue-900">{{ number_format($totalInQty) }}</p>
                <p class="text-xs text-blue-600">Rp {{ number_format($totalInValue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="text-sm text-orange-700">Total Barang Keluar</p>
                <p class="text-2xl font-bold text-orange-900">{{ number_format($totalOutQty) }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-700">Produk Bergerak</p>
                <p class="text-2xl font-bold text-green-900">{{ $uniqueProducts }}</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <p class="text-sm text-purple-700">Transaksi</p>
                <p class="text-2xl font-bold text-purple-900">{{ $totalTransactions }}</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">Periode</p>
                <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
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
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                    <select name="product_id" id="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Produk</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ $productId == $prod->id ? 'selected' : '' }}>{{ $prod->code }} - {{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="in" {{ $type === 'in' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Barang Keluar</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                    @if($categoryId || $locationId || $productId || $type !== 'all' || $dateFrom !== now()->subDays(30)->format('Y-m-d') || $dateTo !== now()->format('Y-m-d'))
                        <a href="{{ route('reports.stock-movement') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Movements Table -->
        <div class="overflow-x-auto">
            @if($movements->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori / Lokasi</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Satuan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Kuantitas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Harga Beli</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-36">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($movements as $item)
                            @php
                                $jenis = $item->type === 'in' ? 'MASUK' : 'KELUAR';
                                $badgeClass = $item->type === 'in' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';
                                $itemValue = $item->quantity * ($item->purchase_price ?? 0);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900">
                                    {{ $item->reference }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $jenis }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product_code }}</div>
                                    <div class="text-sm text-gray-600">{{ $item->product_name }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item->category_name ?? '-' }} / {{ $item->location_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $item->unit }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                    {{ number_format($item->quantity) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">
                                    @if($item->purchase_price)
                                        Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                    @if($item->purchase_price)
                                        Rp {{ number_format($itemValue, 0, ',', '.') }}
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pergerakan stok</h3>
                    <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi pada periode dan filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection