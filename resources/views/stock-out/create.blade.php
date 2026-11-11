@extends('layouts.app')

@section('title', 'Barang Keluar')

@section('content')
<div class="max-w-5xl">
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
            <h2 class="text-2xl font-semibold text-gray-900">Barang Keluar</h2>
            <span class="px-3 py-1 text-sm font-medium bg-orange-100 text-orange-800 rounded-full">
                Stok akan berkurang - validasi otomatis
            </span>
        </div>

        <form action="{{ route('stock-out.store') }}" method="POST" id="stockOutForm">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
                <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Transaksi <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="transaction_date"
                        name="transaction_date"
                        value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('transaction_date') border-red-500 @enderror"
                    >
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                        Tujuan / Keperluan <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="purpose"
                        name="purpose"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('purpose') border-red-500 @enderror"
                    >
                        <option value="">Pilih Tujuan</option>
                        <option value="Penjualan" {{ old('purpose') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                        <option value="Penggunaan Internal" {{ old('purpose') == 'Penggunaan Internal' ? 'selected' : '' }}>Penggunaan Internal</option>
                        <option value="Peminjaman" {{ old('purpose') == 'Peminjaman' ? 'selected' : '' }}>Peminjaman</option>
                        <option value="Rusak/Hilang" {{ old('purpose') == 'Rusak/Hilang' ? 'selected' : '' }}>Rusak/Hilang</option>
                        <option value="Lainnya" {{ old('purpose') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="recipient" class="block text-sm font-medium text-gray-700 mb-2">
                        Penerima
                    </label>
                    <input
                        type="text"
                        id="recipient"
                        name="recipient"
                        value="{{ old('recipient') }}"
                        maxlength="150"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Nama penerima / customer (opsional)"
                    >
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Catatan tambahan (opsional)"
                >{{ old('notes') }}</textarea>
            </div>

            <!-- Items Table -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Barang</h3>
                    <button type="button" id="addItemBtn" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Barang
                    </button>
                </div>

                @if($errors->has('items'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first('items') }}
                    </div>
                @endif

                @if($errors->has('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori / Lokasi</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Stok Tersedia</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Kuantitas <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="itemsTableBody">
                            <!-- Rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 focus:ring-4 focus:ring-orange-200">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Transaksi
                </button>
                <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Product Data for JavaScript -->
<script>
    const productsData = @json($productsData);

    let itemCounter = 0;
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsTableBody = document.getElementById('itemsTableBody');
    const addItemBtn = document.getElementById('addItemBtn');

    // Add first row on load
    addItemRow();

    addItemBtn.addEventListener('click', addItemRow);

    function addItemRow() {
        itemCounter++;
        const row = document.createElement('tr');
        row.dataset.itemIndex = itemCounter;
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-500 text-center">${itemCounter}</td>
            <td class="px-4 py-3">
                <select name="items[${itemCounter}][product_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent product-select" onchange="updateProductInfo(this, ${itemCounter})">
                    <option value="">Pilih Produk</option>
                    ${productsData.map(p => `<option value="${p.id}" data-stock="${p.stock}" data-unit="${p.unit}" data-category="${p.category}" data-location="${p.location}">${p.code} - ${p.name}</option>`).join('')}
                </select>
                <div class="error-message mt-1 text-sm text-red-600 hidden" id="error-product-${itemCounter}"></div>
            </td>
            <td class="px-4 py-3">
                <div class="text-sm text-gray-600 product-info" id="productInfo-${itemCounter}">Pilih produk terlebih dahulu</div>
            </td>
            <td class="px-4 py-3 text-right">
                <span class="text-sm font-medium text-gray-900" id="availableStock-${itemCounter}">-</span>
            </td>
            <td class="px-4 py-3 text-right">
                <input type="number" name="items[${itemCounter}][quantity]" value="1" min="1" required class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right quantity-input" oninput="validateQuantity(this, ${itemCounter})">
                <div class="error-message mt-1 text-sm text-red-600 hidden" id="error-quantity-${itemCounter}"></div>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeItemRow(${itemCounter})" class="text-red-600 hover:text-red-900" title="Hapus">
                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </td>
        `;
        itemsTableBody.appendChild(row);
        updateRowNumbers();
    }

    function removeItemRow(index) {
        const row = document.querySelector(`tr[data-item-index="${index}"]`);
        if (row) {
            row.remove();
            updateRowNumbers();
        }
    }

    function updateRowNumbers() {
        const rows = itemsTableBody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            const newIndex = index + 1;
            row.dataset.itemIndex = newIndex;
            row.cells[0].textContent = newIndex;

            // Update select name
            const select = row.querySelector('select');
            if (select) {
                select.name = `items[${newIndex}][product_id]`;
                select.onchange = () => updateProductInfo(select, newIndex);
            }

            // Update quantity input
            const qtyInput = row.querySelector('.quantity-input');
            if (qtyInput) {
                qtyInput.name = `items[${newIndex}][quantity]`;
                qtyInput.oninput = () => validateQuantity(qtyInput, newIndex);
            }

            // Update product info div
            const infoDiv = row.querySelector('.product-info');
            if (infoDiv) {
                infoDiv.id = `productInfo-${newIndex}`;
            }

            // Update available stock span
            const stockSpan = row.querySelector('[id^="availableStock-"]');
            if (stockSpan) {
                stockSpan.id = `availableStock-${newIndex}`;
            }

            // Update error message divs
            const errorProduct = row.querySelector('[id^="error-product-"]');
            if (errorProduct) {
                errorProduct.id = `error-product-${newIndex}`;
            }
            const errorQuantity = row.querySelector('[id^="error-quantity-"]');
            if (errorQuantity) {
                errorQuantity.id = `error-quantity-${newIndex}`;
            }

            // Update remove button
            const removeBtn = row.querySelector('button[type="button"]');
            if (removeBtn) {
                removeBtn.onclick = () => removeItemRow(newIndex);
            }
        });
        itemCounter = rows.length;
    }

    function updateProductInfo(select, index) {
        const selectedOption = select.options[select.selectedIndex];
        const infoDiv = document.getElementById(`productInfo-${index}`);
        const stockSpan = document.getElementById(`availableStock-${index}`);
        const qtyInput = select.closest('tr').querySelector('.quantity-input');

        if (selectedOption.value) {
            const stock = selectedOption.dataset.stock;
            const unit = selectedOption.dataset.unit;
            const category = selectedOption.dataset.category;
            const location = selectedOption.dataset.location;

            infoDiv.innerHTML = `<span class="font-medium">${category} / ${location}</span>`;
            stockSpan.textContent = `${stock} ${unit}`;

            // Store max stock for validation
            qtyInput.dataset.maxStock = stock;
        } else {
            infoDiv.textContent = 'Pilih produk terlebih dahulu';
            stockSpan.textContent = '-';
        }
    }

    function validateQuantity(input, index) {
        const maxStock = parseInt(input.dataset.maxStock) || 0;
        const qty = parseInt(input.value) || 0;

        if (qty < 1) {
            input.setCustomValidity('Kuantitas minimal 1');
            input.classList.add('border-red-500');
        } else if (qty > maxStock && maxStock > 0) {
            input.setCustomValidity(`Kuantitas melebihi stok tersedia (${maxStock})`);
            input.classList.add('border-red-500');
        } else {
            input.setCustomValidity('');
            input.classList.remove('border-red-500');
        }
        input.reportValidity();
    }

    // Display server-side validation errors for dynamic rows
    function displayServerErrors() {
        @if($errors->any())
            const errorBag = @json($errors->toArray());
            
            // Handle items.* errors
            Object.keys(errorBag).forEach(key => {
                if (key.startsWith('items.')) {
                    const match = key.match(/items\.(\d+)\.(product_id|quantity)/);
                    if (match) {
                        const rowIndex = match[1];
                        const field = match[2];
                        const errorDiv = document.getElementById(`error-${field}-${rowIndex}`);
                        if (errorDiv) {
                            errorDiv.textContent = errorBag[key][0];
                            errorDiv.classList.remove('hidden');
                        }
                        // Also add border to the input/select
                        const input = errorDiv.previousElementSibling;
                        if (input && (input.tagName === 'SELECT' || input.tagName === 'INPUT')) {
                            input.classList.add('border-red-500');
                        }
                    }
                }
            });
        @endif
    }

    // Call on load to display any server-side errors
    displayServerErrors();

    // Form validation before submit
    document.getElementById('stockOutForm').addEventListener('submit', function(e) {
        const rows = itemsTableBody.querySelectorAll('tr');
        const productIds = new Set();

        let hasError = false;

        rows.forEach(row => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('.quantity-input');

            if (!select.value) {
                select.classList.add('border-red-500');
                hasError = true;
            } else {
                select.classList.remove('border-red-500');

                // Check duplicate products
                if (productIds.has(select.value)) {
                    select.classList.add('border-red-500');
                    hasError = true;
                }
                productIds.add(select.value);
            }

            if (!qtyInput.value || parseInt(qtyInput.value) < 1) {
                qtyInput.classList.add('border-red-500');
                hasError = true;
            } else {
                qtyInput.classList.remove('border-red-500');
            }

            // Check stock availability
            const maxStock = parseInt(qtyInput.dataset.maxStock) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            if (qty > maxStock && maxStock > 0) {
                qtyInput.classList.add('border-red-500');
                hasError = true;
            }
        });

        if (rows.length === 0) {
            alert('Minimal satu barang harus ditambahkan');
            e.preventDefault();
            return false;
        }

        if (hasError) {
            alert('Harap perbaiki kesalahan pada form (stok tidak mencukupi atau produk duplikat)');
            e.preventDefault();
            return false;
        }

        return true;
    });
});
</script>
@endpush