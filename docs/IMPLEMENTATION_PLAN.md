# Implementation Plan

Kerjakan berurutan. Jangan melompat ke laporan sebelum alur stok benar.

## Phase 0 — Audit proyek
- [ ] Periksa versi PHP, Laravel, Node, Composer, dan database.
- [ ] Periksa Git status dan struktur repository.
- [ ] Periksa apakah autentikasi/layout sudah tersedia.
- [ ] Cocokkan implementasi dengan dokumen ini.
**Output:** catatan kondisi awal.

## Phase 1 — Fondasi
- [ ] Setup Laravel dan koneksi MySQL.
- [ ] Setup Tailwind CSS 4/Vite sesuai versi proyek.
- [ ] Siapkan autentikasi login/logout.
- [ ] Tambahkan role Admin/Staff dan status akun.
- [ ] Buat layout dashboard dan proteksi route.
- [ ] Buat seeder Admin development dengan kredensial yang aman dan didokumentasikan.
**Acceptance:** user dapat login/logout; Staff tidak dapat membuka route Admin.

## Phase 2 — Master data
- [ ] Migration kategori dan lokasi.
- [ ] Migration produk.
- [ ] Model dan relasi Eloquent.
- [ ] CRUD kategori/lokasi.
- [ ] CRUD produk tanpa edit stok.
- [ ] Pencarian, filter, pagination.
**Acceptance:** data produk dapat dikelola dan referensi kategori/lokasi valid.

## Phase 3 — Transaksi stok
- [ ] Migration header/detail barang masuk.
- [ ] Migration header/detail barang keluar.
- [ ] Form transaksi dengan dynamic rows.
- [ ] Pembuatan kode transaksi yang aman.
- [ ] DB transaction dan lockForUpdate.
- [ ] Validasi stok dan rollback.
- [ ] Activity log transaksi.
**Acceptance:** stok masuk/keluar konsisten dan tidak negatif.

## Phase 4 — Dashboard dan histori
- [ ] Ringkasan dashboard berbasis data.
- [ ] Daftar stok rendah.
- [ ] Riwayat transaksi gabungan.
- [ ] Halaman detail transaksi.
**Acceptance:** informasi sesuai database dan filter bekerja.

## Phase 5 — Laporan dan pengguna
- [ ] Laporan stok saat ini.
- [ ] Laporan transaksi berdasarkan filter.
- [ ] Ekspor Excel/PDF jika disetujui.
- [ ] Manajemen pengguna Admin.
- [ ] Activity log viewer Admin.
**Acceptance:** role dan hasil laporan sesuai.

## Phase 6 — Pengujian dan deployment
- [ ] Feature tests untuk alur kritis.
- [ ] Black Box Testing.
- [ ] Uji responsif dan validasi.
- [ ] Review keamanan.
- [ ] Dokumentasi instalasi dan user guide.
- [ ] Backup/restore database diuji.
- [ ] Persiapkan screenshot dan bahan laporan magang.