@extends('layouts.app')

@section('title', 'Activity Logs')

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
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Activity Logs</h2>

        <!-- Filter Form -->
        <form method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Aksi</label>
                    <select name="action" id="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pengguna</label>
                    <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
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
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Deskripsi, aksi, user..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Cari</button>
                            @if($action || $userId || $dateFrom || $dateTo || $search)
                                <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Logs Table -->
        <div class="overflow-x-auto">
            @if($logs->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <div class="font-medium">{{ $log->user->name ?? 'System' }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClass = 'bg-gray-100 text-gray-800';
                                        if (str_starts_with($log->action, 'stock_in')) $badgeClass = 'bg-blue-100 text-blue-800';
                                        elseif (str_starts_with($log->action, 'stock_out')) $badgeClass = 'bg-orange-100 text-orange-800';
                                        elseif (str_starts_with($log->action, 'product')) $badgeClass = 'bg-green-100 text-green-800';
                                        elseif (str_starts_with($log->action, 'category') || str_starts_with($log->action, 'location')) $badgeClass = 'bg-purple-100 text-purple-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-md">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4 flex justify-center">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada log aktivitas</h3>
                    <p class="mt-1 text-sm text-gray-500">Log aktivitas akan muncul di sini setelah ada transaksi atau perubahan data.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection