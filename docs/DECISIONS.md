# Architecture Decision Records (ADR)

## ADR-001 — Laravel MVC + Blade
Status: Accepted
Keputusan: menggunakan Laravel MVC, Blade, Tailwind CSS, dan vanilla JavaScript.
Alasan: sesuai kebutuhan aplikasi internal, mudah dipelihara, dan mudah dijelaskan dalam laporan magang.

## ADR-002 — Satu lokasi per produk
Status: Accepted for MVP
Keputusan: `products.location_id` menyimpan satu lokasi untuk setiap produk.
Konsekuensi: stok produk tidak dibagi ke beberapa gudang dalam MVP.

## ADR-003 — Dua role
Status: Accepted
Keputusan: role `admin` dan `staff`.
Konsekuensi: otorisasi server-side wajib.

## ADR-004 — Saldo stok tersimpan pada products
Status: Accepted
Keputusan: `products.stock` menyimpan saldo saat ini; detail transaksi menyimpan histori pergerakan.
Konsekuensi: setiap perubahan saldo harus dilakukan bersama pencatatan transaksi dalam satu database transaction.

## ADR-005 — Saldo awal melalui transaksi masuk
Status: Accepted
Keputusan: saldo awal dicatat sebagai transaksi masuk dengan sumber "Saldo awal".
Alasan: setiap saldo memiliki jejak pencatatan.

## ADR-006 — Transaksi posted tidak dapat diedit/hapus
Status: Accepted for MVP
Keputusan: transaksi yang sudah berhasil disimpan bersifat immutable melalui UI normal.
Konsekuensi: koreksi membutuhkan prosedur transaksi koreksi yang transparan; fitur koreksi khusus dapat ditambahkan setelah MVP bila dibutuhkan.