# Architecture — Laravel MVC

## 1. Gambaran
Aplikasi menggunakan Laravel MVC:
- Model: data, relasi, dan aturan relasi Eloquent.
- View: Blade templates dan komponen Blade.
- Controller: menerima request, menjalankan validasi/otorisasi, memanggil operasi model, dan menentukan response.
- Form Request: validasi input dan otorisasi request.
- Middleware/Policy: pembatasan akses.

Untuk MVP tidak diperlukan API terpisah, SPA, repository pattern, atau microservices.

## 2. Struktur direktori yang disarankan
```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── LocationController.php
│   │   ├── StockInController.php
│   │   ├── StockOutController.php
│   │   ├── TransactionHistoryController.php
│   │   ├── ReportController.php
│   │   └── UserController.php
│   ├── Middleware/
│   └── Requests/
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Location.php
│   ├── StockIn.php
│   ├── StockInItem.php
│   ├── StockOut.php
│   ├── StockOutItem.php
│   └── ActivityLog.php
database/
├── migrations/
├── factories/
└── seeders/
resources/
├── views/
│   ├── layouts/
│   ├── components/
│   ├── auth/
│   ├── dashboard/
│   ├── products/
│   ├── categories/
│   ├── locations/
│   ├── stock-in/
│   ├── stock-out/
│   ├── transactions/
│   ├── reports/
│   └── users/
├── css/
└── js/
routes/web.php
tests/
```

Struktur dapat menyesuaikan Laravel 13 yang terpasang; jangan membuat folder kosong hanya demi mengikuti diagram.

## 3. Konvensi model
- Gunakan nama model singular PascalCase.
- Definisikan `fillable` atau `guarded` secara sadar.
- Definisikan relasi `belongsTo`, `hasMany`.
- Cast boolean, date, dan decimal sesuai kebutuhan.
- Jangan menaruh seluruh proses transaksi kompleks pada accessor atau view.

## 4. Routes
- Gunakan `auth` middleware untuk halaman internal.
- Gunakan middleware role atau Policy untuk fitur Admin.
- Gunakan resource route untuk CRUD master data.
- Pisahkan endpoint penyimpanan transaksi masuk dan keluar.
- Endpoint ekspor laporan menggunakan GET dengan validasi filter.
- Jangan menerima `user_id` pembuat dari form; ambil dari user yang sedang login.

Contoh konseptual:
```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('locations', LocationController::class);

    Route::get('/stock-in/create', [StockInController::class, 'create'])->name('stock-in.create');
    Route::post('/stock-in', [StockInController::class, 'store'])->name('stock-in.store');

    Route::get('/stock-out/create', [StockOutController::class, 'create'])->name('stock-out.create');
    Route::post('/stock-out', [StockOutController::class, 'store'])->name('stock-out.store');

    Route::get('/transactions', [TransactionHistoryController::class, 'index'])->name('transactions.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
```
Ini contoh desain, bukan pengganti pemeriksaan route Laravel aktual.

## 5. Controller dan Form Request
Pisahkan validasi ke Form Request saat sesuai. Gunakan redirect dengan session flash untuk pola web tradisional (PRG: Post/Redirect/Get). Gunakan eager loading untuk mencegah N+1 query.

## 6. Database transaction
Gunakan `DB::transaction()` untuk setiap transaksi stok. Stok dan detail histori harus konsisten. Jangan update stok dalam event yang tersembunyi sehingga sulit ditelusuri.

## 7. UI
Blade layout utama memuat sidebar, topbar, content slot, dan flash messages. Komponen reusable untuk button, input, select, badge, table, pagination, dan modal.