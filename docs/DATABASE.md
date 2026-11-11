# Database Specification

Database: MySQL / MariaDB, InnoDB, utf8mb4.

## 1. `users`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| name | VARCHAR(100) | NOT NULL |
| email | VARCHAR(150) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL, hashed |
| role | VARCHAR(20) | `admin` atau `staff` |
| is_active | BOOLEAN | default true |
| created_at, updated_at | TIMESTAMP | Laravel timestamps |

Catatan: pertahankan kolom standar Laravel yang dibutuhkan autentikasi, misalnya `remember_token`.

## 2. `categories`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| name | VARCHAR(100) | UNIQUE, NOT NULL |
| description | TEXT | nullable |
| created_at, updated_at | TIMESTAMP | |

## 3. `locations`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| name | VARCHAR(100) | UNIQUE, NOT NULL |
| description | TEXT | nullable |
| created_at, updated_at | TIMESTAMP | |

## 4. `products`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| code | VARCHAR(50) | UNIQUE, NOT NULL |
| name | VARCHAR(150) | NOT NULL |
| category_id | BIGINT UNSIGNED | FK categories |
| location_id | BIGINT UNSIGNED | FK locations |
| brand | VARCHAR(100) | nullable |
| item_type | VARCHAR(30) | `trade_goods`, `company_asset`, `operational_supply` |
| unit | VARCHAR(30) | NOT NULL, contoh pcs/unit/box |
| purchase_price | DECIMAL(15,2) | nullable atau default 0 sesuai kebutuhan |
| selling_price | DECIMAL(15,2) | nullable atau default 0 sesuai kebutuhan |
| stock | INT UNSIGNED | default 0 |
| minimum_stock | INT UNSIGNED | default 0 |
| description | TEXT | nullable |
| is_active | BOOLEAN | default true |
| created_at, updated_at | TIMESTAMP | |

Indeks: unique `code`, index `name`, `category_id`, `location_id`, `is_active`. Harga hanya diperlukan jika relevan dengan proses bisnis; jangan gunakan harga nol untuk menyamarkan data yang tidak diketahui.

## 5. `stock_ins`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| transaction_code | VARCHAR(50) | UNIQUE, NOT NULL |
| transaction_date | DATE | NOT NULL |
| source | VARCHAR(150) | NOT NULL |
| notes | TEXT | nullable |
| user_id | BIGINT UNSIGNED | FK users |
| created_at, updated_at | TIMESTAMP | |

Index `transaction_date`, `user_id`.

## 6. `stock_in_items`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| stock_in_id | BIGINT UNSIGNED | FK stock_ins |
| product_id | BIGINT UNSIGNED | FK products |
| quantity | INT UNSIGNED | > 0 |
| purchase_price | DECIMAL(15,2) | nullable; snapshot harga saat transaksi |
| created_at, updated_at | TIMESTAMP | |

Unique gabungan `(stock_in_id, product_id)` untuk mencegah baris produk duplikat dalam satu transaksi.

## 7. `stock_outs`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| transaction_code | VARCHAR(50) | UNIQUE, NOT NULL |
| transaction_date | DATE | NOT NULL |
| purpose | VARCHAR(100) | NOT NULL |
| recipient | VARCHAR(150) | nullable |
| notes | TEXT | nullable |
| user_id | BIGINT UNSIGNED | FK users |
| created_at, updated_at | TIMESTAMP | |

Index `transaction_date`, `user_id`.

## 8. `stock_out_items`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| stock_out_id | BIGINT UNSIGNED | FK stock_outs |
| product_id | BIGINT UNSIGNED | FK products |
| quantity | INT UNSIGNED | > 0 |
| created_at, updated_at | TIMESTAMP | |

Unique gabungan `(stock_out_id, product_id)`.

## 9. `activity_logs`
| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | PK |
| user_id | BIGINT UNSIGNED | FK users, nullable jika diperlukan untuk aktivitas sistem |
| action | VARCHAR(100) | NOT NULL |
| description | TEXT | NOT NULL |
| created_at | TIMESTAMP | waktu kejadian |

Tidak perlu `updated_at`: log bersifat historis.

## Foreign key policy
- `products.category_id` dan `products.location_id`: `RESTRICT` saat masih direferensikan.
- Detail transaksi ke header: `RESTRICT`/`CASCADE` hanya jika transaksi memang dapat dihapus sebelum diposting. Rekomendasi MVP: transaksi posted tidak dihapus; gunakan `RESTRICT`.
- Detail transaksi ke produk: `RESTRICT`.
- Transaksi ke user: `RESTRICT`, atau gunakan akun nonaktif agar histori tetap utuh.
- Log ke user: pertimbangkan `SET NULL` agar histori tetap ada ketika akun dinonaktifkan/dihapus sesuai kebijakan.

## Aturan implementasi stok
### Barang masuk
Dalam satu `DB::transaction()`:
1. Validasi header dan seluruh detail.
2. Buat header `stock_ins`.
3. Untuk setiap detail, kunci baris produk dengan `lockForUpdate()`.
4. Pastikan produk aktif.
5. Tambahkan kuantitas ke `products.stock`.
6. Buat `stock_in_items`.
7. Buat activity log.
8. Commit otomatis; rollback jika ada kegagalan.

### Barang keluar
Dalam satu `DB::transaction()`:
1. Validasi header dan seluruh detail.
2. Buat header `stock_outs`.
3. Urutkan ID produk agar urutan lock konsisten.
4. Kunci baris produk dengan `lockForUpdate()`.
5. Pastikan produk aktif dan stok cukup.
6. Kurangi `products.stock`.
7. Buat `stock_out_items`.
8. Buat activity log.
9. Commit otomatis; rollback jika ada kegagalan.

Jangan hanya memeriksa stok sebelum transaksi database dimulai; dua permintaan bersamaan dapat menyebabkan race condition.

## Format kode transaksi
Gunakan format yang mudah dibaca, misalnya `IN-YYYYMMDD-XXXX` dan `OUT-YYYYMMDD-XXXX`. Nomor urut harus dibuat dengan cara aman terhadap request bersamaan. Jangan mengandalkan `count()+1` tanpa mekanisme penguncian/unique retry.

## Catatan saldo awal
Masukkan saldo awal melalui transaksi barang masuk dengan sumber `Saldo awal`. Ini menjaga histori dan menghindari perubahan stok yang tidak terlacak.