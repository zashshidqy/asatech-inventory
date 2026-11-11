@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="space-y-6">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Produk
        </a>
    </div>

    <!-- Product Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">{{ $product->code }} - {{ $product->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    Edit
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode</label>
                <p class="text-gray-900 font-mono">{{ $product->code }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama</label>
                <p class="text-gray-900">{{ $product->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Merek</label>
                <p class="text-gray-900">{{ $product->brand ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                <p class="text-gray-900">{{ $product->category->name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Lokasi</label>
                <p class="text-gray-900">{{ $product->location->name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tipe Barang</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($product->item_type === 'trade_goods') bg-green-100 text-green-800
                    @elseif($product->item_type === 'company_asset') bg-purple-100 text-purple-800
                    @else bg-orange-100 text-orange-800 @endif">
                    {{ $product->item_type === 'trade_goods' ? 'Barang Dagang' : ($product->item_type === 'company_asset' ? 'Aset Perusahaan' : 'Perlengkapan Operasional') }}
                </span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Satuan</label>
                <p class="text-gray-900">{{ $product->unit }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Harga Beli</label>
                <p class="text-gray-900">{{ $product->purchase_price ? 'Rp ' . number_format($product->purchase_price, 0, ',', '.') : '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Harga Jual</label>
                <p class="text-gray-900">{{ $product->selling_price ? 'Rp ' . number_format($product->selling_price, 0, ',', '.') : '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Minimum</label>
                <p class="text-gray-900">{{ $product->minimum_stock }} {{ $product->unit }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <div class="lg:col-span-3">
                <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                <p class="text-gray-900">{{ $product->description ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat</label>
                <p class="text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diperbarui</label>
                <p class="text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Stock Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Stok</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-500">Stok Saat Ini</label>
                <p class="text-3xl font-bold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $product->stock }} {{ $product->unit }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-500">Stok Minimum</label>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $product->minimum_stock }} {{ $product->unit }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-500">Status Stok</label>
                <p class="text-3xl font-bold {{ $product->isLowStock() ? 'text-red-600' : 'text-green-600' }} mt-1">
                    {{ $product->isLowStock() ? 'RENDAH' : 'NORMAL' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="space-y-6">
        <!-- Stock In History -->
        @if($product->stockInItems->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Barang Masuk</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kuantitas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($product->stockInItems->sortByDesc('stockIn.transaction_date') as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $item->stockIn->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-mono text-sm">{{ $item->stockIn->transaction_code }}</td>
                                <td class="px-6 py-4">{{ $item->stockIn->source }}</td>
                                <td class="px-6 py-4 text-right">{{ $item->quantity }} {{ $product->unit }}</td>
                                <td class="px-6 py-4 text-right">{{ $item->purchase_price ? 'Rp ' . number_format($item->purchase_price, 0, ',', '.') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Stock Out History -->
        @if($product->stockOutItems->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Barang Keluar</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kuantitas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($product->stockOutItems->sortByDesc('stockOut.transaction_date') as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $item->stockOut->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-mono text-sm">{{ $item->stockOut->transaction_code }}</td>
                                <td class="px-6 py-4">{{ $item->stockOut->purpose }}</td>
                                <td class="px-6 py-4">{{ $item->stockOut->recipient ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">{{ $item->quantity }} {{ $product->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($product->stockInItems->count() == 0 && $product->stockOutItems->count() == 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-2 text-gray-500">Belum ada riwayat transaksi untuk produk ini</p>
            </div>
        @endif
    </div>
</div>
@endsection