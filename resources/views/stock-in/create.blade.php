@extends('layouts.app')

@section('title', 'Barang Masuk')

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
            <h2 class="text-2xl font-semibold text-gray-900">Barang Masuk</h2>
            <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                Stok akan bertambah otomatis
            </span>
        </div>

        <form action="{{ route('stock-in.store') }}" method="POST" id="stockInForm">
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
                    <label for="source" class="block text-sm font-medium text-gray-700 mb-2">
                        Sumber <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="source"
                        name="source"
                        value="{{ old('source') }}"
                        required
                        maxlength="150"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('source') border-red-500 @enderror"
                        placeholder="Contoh: Supplier PT. Maju Jaya / Saldo awal"
                    >
                    @error('source')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Catatan tambahan (opsional)"
                    >{{ old('notes') }}</textarea>
                </div>
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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori / Lokasi</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Stok Tersedia</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Kuantitas <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-40">Harga Beli</th>
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
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200">
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
                    ${productsData.map(p => `<option value="${p.id}" data-stock="${p.stock}" data-unit="${p.unit}" data-category="${p.category}" data-location="${p.location}" data-price="${p.purchase_price || ''}">${p.code} - ${p.name}</option>`).join('')}
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
            <td class="px-4 py-3 text-right">
                <input type="number" name="items[${itemCounter}][purchase_price]" value="" step="0.01" min="0" class="w-40 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right" placeholder="Opsional">
                <div class="error-message mt-1 text-sm text-red-600 hidden" id="error-price-${itemCounter}"></div>
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

            // Update price input
            const priceInput = row.querySelector('input[type="number"]:not(.quantity-input)');
            if (priceInput) {
                priceInput.name = `items[${newIndex}][purchase_price]`;
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
            const errorPrice = row.querySelector('[id^="error-price-"]');
            if (errorPrice) {
                errorPrice.id = `error-price-${newIndex}`;
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
        const priceInput = select.closest('tr').querySelector('input[type="number"]:not(.quantity-input)');

        if (selectedOption.value) {
            const stock = selectedOption.dataset.stock;
            const unit = selectedOption.dataset.unit;
            const category = selectedOption.dataset.category;
            const location = selectedOption.dataset.location;
            const price = selectedOption.dataset.price;

            infoDiv.innerHTML = `<span class="font-medium">${category} / ${location}</span>`;
            stockSpan.textContent = `${stock} ${unit}`;

            // Pre-fill purchase price if available
            if (price && !priceInput.value) {
                priceInput.value = price;
            }

            // Set max quantity warning (optional, just visual)
            qtyInput.dataset.maxStock = stock;
        } else {
            infoDiv.textContent = 'Pilih produk terlebih dahulu';
            stockSpan.textContent = '-';
        }
    }

    function validateQuantity(input, index) {
        const maxStock = parseInt(input.dataset.maxStock) || 0;
        const qty = parseInt(input.value) || 0;

        // Just show warning, don't block (stock in can exceed current stock)
        if (qty < 1) {
            input.setCustomValidity('Kuantitas minimal 1');
        } else {
            input.setCustomValidity('');
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
                    const match = key.match(/items\.(\d+)\.(product_id|quantity|purchase_price)/);
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
    document.getElementById('stockInForm').addEventListener('submit', function(e) {
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
        });

        if (rows.length === 0) {
            alert('Minimal satu barang harus ditambahkan');
            e.preventDefault();
            return false;
        }

        if (hasError) {
            alert('Harap perbaiki kesalahan pada form');
            e.preventDefault();
            return false;
        }

        return true;
    });
});
</script>
@endpush