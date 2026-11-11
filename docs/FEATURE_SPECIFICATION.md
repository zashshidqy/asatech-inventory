# Feature Specification

## Dashboard
- Kartu total jenis barang aktif.
- Total unit stok (jumlah seluruh `products.stock` untuk produk aktif).
- Jumlah transaksi masuk dan keluar pada bulan/periode terpilih.
- Daftar produk dengan `stock <= minimum_stock`.
- Aktivitas terbaru.
- Angka dashboard harus berasal dari query database, bukan hardcoded.

## Produk
Field form:
- kode barang (unik)
- nama barang
- kategori
- lokasi
- merek (opsional)
- tipe barang: barang dagang/aset perusahaan/perlengkapan operasional
- satuan
- harga beli dan harga jual (opsional sesuai kebutuhan)
- stok minimum
- deskripsi
- status aktif

Stok saat ini hanya tampil sebagai informasi; tidak dapat diubah di form produk.
Pencarian: kode atau nama.
Filter: kategori, lokasi, status stok (normal/rendah), status aktif.
Daftar memakai pagination.

## Kategori dan lokasi
- Daftar, tambah, ubah, dan nonaktifkan/hapus bila tidak direferensikan.
- Tolak penghapusan jika masih digunakan produk.
- Nama unik.

## Barang masuk
Header: tanggal, sumber, catatan.
Detail: produk, kuantitas, harga beli saat transaksi (opsional).
Minimal satu baris detail.
Produk tidak boleh duplikat dalam satu transaksi.
Setelah berhasil, stok bertambah.

## Barang keluar
Header: tanggal, tujuan/purpose, penerima (opsional), catatan.
Detail: produk dan kuantitas.
Minimal satu baris detail.
Sistem menolak transaksi bila stok tidak mencukupi.
Setelah berhasil, stok berkurang.

Contoh purpose dapat berupa penjualan, penggunaan internal, peminjaman, atau lainnya. Sesuaikan daftar final dengan kebijakan AsaTech.

## Riwayat transaksi
- Gabungan transaksi masuk dan keluar.
- Filter tanggal, tipe transaksi, pembuat, dan kode.
- Halaman detail menampilkan semua baris barang.
- Tampilkan kode transaksi, tanggal, pembuat, dan catatan.

## Laporan
- Stok saat ini.
- Rekap transaksi masuk.
- Rekap transaksi keluar.
- Filter tanggal, produk, kategori, dan lokasi.
- Ekspor Excel/PDF bersifat opsional dan dikerjakan setelah laporan layar stabil.

## Manajemen pengguna
- Admin dapat membuat akun Staff/Admin.
- Role hanya `admin` atau `staff`.
- Akun nonaktif tidak dapat login.
- Password awal harus ditetapkan dengan alur aman; jangan tampilkan password plaintext di activity log.
- Hindari penghapusan akun yang sudah memiliki histori.

## Activity log
Catat aksi penting: `product.created`, `product.updated`, `product.deactivated`, `stock_in.created`, `stock_out.created`, `user.created`, dan perubahan master data.
Hindari menyimpan password, token, atau data sensitif dalam deskripsi.