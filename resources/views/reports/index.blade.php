@extends('layouts.app')

@section('title', 'Laporan')

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
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Laporan Inventaris</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Current Stock Report Card -->
            <a href="{{ route('reports.current-stock') }}" class="block p-6 bg-gray-50 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Stok Saat Ini</h3>
                        <p class="text-gray-600 mt-1">Lihat stok real-time semua produk dengan filter kategori, lokasi, dan status stok (habis/rendah/normal).</p>
                        <ul class="mt-3 text-sm text-gray-500 space-y-1">
                            <li>• Filter: Kategori, Lokasi, Status Stok, Pencarian</li>
                            <li>• Tampilkan: Kode, Nama, Stok, Min Stok, Harga, Nilai Stok</li>
                            <li>• Export ke CSV</li>
                        </ul>
                    </div>
                </div>
            </a>

            <!-- Stock Movement Report Card -->
            <a href="{{ route('reports.stock-movement') }}" class="block p-6 bg-gray-50 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Pergerakan Stok</h3>
                        <p class="text-gray-600 mt-1">Lihat riwayat barang masuk & keluar per periode dengan detail kuantitas, harga, dan nilai transaksi.</p>
                        <ul class="mt-3 text-sm text-gray-500 space-y-1">
                            <li>• Filter: Rentang Tanggal, Kategori, Lokasi, Produk, Jenis</li>
                            <li>• Tampilkan: Tanggal, Kode Transaksi, Jenis, Produk, Qty, Nilai</li>
                            <li>• Ringkasan: Total Masuk/Keluar, Nilai, Jumlah Transaksi</li>
                            <li>• Export ke CSV</li>
                        </ul>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection