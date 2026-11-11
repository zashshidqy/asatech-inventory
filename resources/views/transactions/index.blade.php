@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="max-w-7xl">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Riwayat Transaksi</h2>
            <div class="flex gap-2">
                <a href="{{ route('stock-in.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Barang Masuk
                </a>
                <a href="{{ route('stock-out.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Barang Keluar
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-700">Total Barang Masuk</p>
                <p class="text-2xl font-bold text-blue-900">{{ $totalIn }}</p>
                <p class="text-xs text-blue-600">{{ $totalItemsIn }} item</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="text-sm text-orange-700">Total Barang Keluar</p>
                <p class="text-2xl font-bold text-orange-900">{{ $totalOut }}</p>
                <p class="text-xs text-orange-600">{{ $totalItemsOut }} item</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-700">Total Transaksi</p>
                <p class="text-2xl font-bold text-green-900">{{ $totalIn + $totalOut }}</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">Filter Aktif</p>
                <p class="text-lg font-bold text-gray-900">
                    {{ $type === 'all' ? 'Semua' : ($type === 'stock_in' ? 'Masuk' : 'Keluar') }}
                </p>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="stock_in" {{ $type === 'stock_in' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="stock_out" {{ $type === 'stock_out' ? 'selected' : '' }}>Barang Keluar</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Kode transaksi, produk, sumber, tujuan..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Cari</button>
                        @if($type || $dateFrom || $dateTo || $search)
                            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            @if(isset($mergedPaginated) && $mergedPaginated->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber / Tujuan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($mergedPaginated as $transaction)
                            @php
                                $isStockIn = $transaction instanceof \App\Models\StockIn;
                                $itemsCount = $transaction->items->count();
                                $totalQty = $transaction->items->sum('quantity');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900">
                                    {{ $transaction->transaction_code }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($isStockIn)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                            Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    @if($isStockIn)
                                        {{ $transaction->source }}
                                    @else
                                        {{ $transaction->purpose }}
                                        @if($transaction->recipient)
                                            <br><span class="text-xs text-gray-500">{{ $transaction->recipient }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    {{ $itemsCount }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                    {{ $totalQty }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $transaction->user->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($isStockIn)
                                        <a href="{{ route('transactions.show-stock-in', $transaction) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Detail</a>
                                    @else
                                        <a href="{{ route('transactions.show-stock-out', $transaction) }}" class="text-orange-600 hover:text-orange-900 text-sm font-medium">Detail</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4 flex justify-center">
                    {{ $mergedPaginated->links() }}
                </div>

            @elseif($type === 'stock_in' && $stockIns->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stockIns as $stockIn)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ \Carbon\Carbon::parse($stockIn->transaction_date)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ $stockIn->transaction_code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $stockIn->source }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $stockIn->items->count() }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ $stockIn->items->sum('quantity') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $stockIn->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('transactions.show-stock-in', $stockIn) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 flex justify-center">{{ $stockIns->links() }}</div>

            @elseif($type === 'stock_out' && $stockOuts->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stockOuts as $stockOut)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ \Carbon\Carbon::parse($stockOut->transaction_date)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ $stockOut->transaction_code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $stockOut->purpose }} @if($stockOut->recipient)<br><span class="text-xs text-gray-500">{{ $stockOut->recipient }}</span>@endif</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $stockOut->items->count() }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ $stockOut->items->sum('quantity') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $stockOut->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('transactions.show-stock-out', $stockOut) }}" class="text-orange-600 hover:text-orange-900 text-sm font-medium">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 flex justify-center">{{ $stockOuts->links() }}</div>

            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan mencatat barang masuk atau barang keluar.</p>
                    <div class="mt-6 flex justify-center gap-2">
                        <a href="{{ route('stock-in.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Barang Masuk</a>
                        <a href="{{ route('stock-out.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Barang Keluar</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection