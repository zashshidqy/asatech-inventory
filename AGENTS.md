# AGENTS.md — Instruksi AI Coding Agent

## 1. Peran
Anda adalah coding agent yang membantu membangun **AsaTech Inventory**, aplikasi web internal untuk pencatatan barang dan pergerakan stok. Dokumentasi dalam repository adalah sumber kebenaran utama.

## 2. Wajib dibaca
Sebelum melakukan perubahan, baca:
- `README.md`
- `docs/PRD.md`
- `docs/ARCHITECTURE.md`
- `docs/DATABASE.md`
- `docs/IMPLEMENTATION_PLAN.md`
- Dokumen fitur terkait dengan tugas saat ini.

## 3. Stack yang ditetapkan
- Laravel 13
- PHP 8.3 atau versi yang kompatibel dengan Laravel 13 (gunakan versi PHP aktual proyek)
- Blade
- Tailwind CSS 4
- Vanilla JavaScript seperlunya
- MySQL / MariaDB
- Laravel MVC
- Git untuk version control

Jangan mengganti stack tanpa persetujuan eksplisit.

## 4. Prinsip implementasi
1. Utamakan solusi sederhana, terbaca, dan mudah dijelaskan dalam laporan magang.
2. Gunakan pola Laravel standar: route, controller, Form Request, model, migration, Blade.
3. Jangan menambahkan repository pattern, service layer, microservices, SPA, REST API terpisah, atau dependency berlebihan tanpa kebutuhan nyata.
4. Gunakan Eloquent relationship dan query builder secara wajar.
5. Gunakan database transaction untuk proses yang mengubah header transaksi, detail, dan stok sekaligus.
6. Gunakan `lockForUpdate()` saat mengambil stok produk untuk transaksi keluar.
7. Jangan mengubah stok secara langsung dari form edit produk.
8. Jangan mengedit atau menghapus transaksi stok yang sudah diposting melalui CRUD biasa. Koreksi harus transparan dan tercatat.
9. Otorisasi wajib dilakukan di server, bukan hanya menyembunyikan tombol/menu.
10. Jangan membuat data perusahaan fiktif seolah-olah data asli. Seeder demo harus diberi label jelas.
11. Jangan menambahkan fitur di luar MVP tanpa persetujuan.
12. Jangan menghapus file atau mengubah bagian yang tidak terkait tugas.

## 5. Cara bekerja
Untuk setiap tugas:
1. Periksa struktur repository dan status Git terlebih dahulu.
2. Periksa implementasi yang sudah ada; jangan mengasumsikan TODO berarti belum dikerjakan.
3. Sampaikan ringkasan rencana singkat.
4. Implementasikan satu lingkup kecil yang selesai secara utuh.
5. Jalankan formatter/linter/test yang tersedia dan relevan.
6. Periksa `git diff` untuk memastikan tidak ada perubahan tak terkait.
7. Perbarui `docs/TODO.md`, `docs/CHANGELOG.md`, dan dokumentasi lain yang terdampak.
8. Laporkan file yang diubah, keputusan, perintah pengujian, hasil, serta pekerjaan berikutnya.

## 6. Aturan database
- Gunakan foreign key dan tipe data yang sesuai.
- Uang menggunakan `DECIMAL`, bukan `FLOAT`.
- Kuantitas harus bilangan bulat positif untuk transaksi.
- Kode produk dan kode transaksi harus unik.
- Stok tidak boleh negatif.
- Hindari cascade delete pada data transaksi historis.
- Gunakan `is_active` untuk menonaktifkan produk/pengguna bila diperlukan.
- Jangan menambahkan kolom atau tabel baru tanpa alasan dan dokumentasi.

## 7. Definisi selesai
Sebuah fitur dianggap selesai jika:
- alur utama berjalan;
- validasi input tersedia;
- otorisasi sesuai role;
- kondisi gagal ditangani;
- tampilan konsisten;
- pengujian relevan lulus;
- dokumentasi dan TODO diperbarui.

## 8. Format laporan akhir agent
- Ringkasan pekerjaan
- File yang dibuat/diubah
- Perilaku yang sekarang tersedia
- Validasi dan otorisasi
- Pengujian yang dijalankan beserta hasil
- Catatan risiko atau asumsi
- Langkah berikutnya