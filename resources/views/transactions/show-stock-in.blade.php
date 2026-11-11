@extends('layouts.app')

@section('title', 'Detail Barang Masuk - {{ $stockIn->transaction_code }}')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('transactions.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Detail Barang Masuk</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $stockIn->transaction_code }}</p>
                </div>
                <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">BARANG MASUK</span>
            </div>
        </div>

        <div class="p-6">
            <!-- Info Header -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Tanggal Transaksi</label>
                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($stockIn->transaction_date)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Sumber</label>
                    <p class="text-gray-900">{{ $stockIn->source }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Dicatat Oleh</label>
                    <p class="text-gray-900">{{ $stockIn->user->name ?? '-' }} <span class="text-gray-500">({{ $stockIn->user->email ?? '-' }})</span></p>
                </div>
            </div>

            @if($stockIn->notes)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Catatan</label>
                    <p class="text-gray-900">{{ $stockIn->notes }}</p>
                </div>
            @endif

            <!-- Items Table -->
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar Barang</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori / Lokasi</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Kuantitas</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-40">Harga Beli</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-48">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $grandTotal = 0;
                            $totalQty = 0;
                        @endphp
                        @foreach($stockIn->items as $index => $item)
                            @php
                                $subtotal = $item->quantity * ($item->purchase_price ?? 0);
                                $grandTotal += $subtotal;
                                $totalQty += $item->quantity;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $item->product->code }} - {{ $item->product->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item->product->category->name ?? '-' }} / {{ $item->product->location->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                    {{ $item->quantity }} {{ $item->product->unit }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">
                                    @if($item->purchase_price)
                                        Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">
                                    @if($item->purchase_price)
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="3" class="px-4 py-3 text-right text-gray-900">TOTAL</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ $totalQty }}</td>
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                @if($grandTotal > 0)
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection