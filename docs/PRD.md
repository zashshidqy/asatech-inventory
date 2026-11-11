# Product Requirements Document (PRD)
## AsaTech Inventory

- Status: Draft baseline
- Product type: Internal web application
- Primary language: Bahasa Indonesia
- Target users: Admin dan Staff AsaTech

## 1. Ringkasan produk
AsaTech Inventory adalah aplikasi berbasis web untuk membantu pencatatan data barang, lokasi penyimpanan, transaksi barang masuk dan keluar, pemantauan stok, serta penyusunan laporan inventaris.

Aplikasi ditujukan untuk penggunaan internal. Sistem ini juga dikembangkan sebagai proyek magang, sehingga implementasinya harus mudah dipahami, diuji, dan dijelaskan.

## 2. Masalah dan tujuan
### Tujuan
- Memusatkan data barang dalam satu aplikasi.
- Menyediakan informasi jumlah stok terkini.
- Mencatat setiap transaksi barang masuk dan keluar.
- Menyediakan riwayat transaksi yang dapat ditelusuri.
- Membantu pengguna mengetahui barang yang mencapai batas stok minimum.
- Menyediakan laporan inventaris berdasarkan filter.

### Catatan analisis
Masalah pada proses berjalan harus ditulis berdasarkan observasi dan wawancara nyata. Jangan mengklaim perusahaan menggunakan pencatatan manual atau mengalami masalah tertentu sebelum dikonfirmasi.

## 3. Sasaran dan indikator keberhasilan
- Admin dan Staff dapat masuk menggunakan akun masing-masing.
- Pengguna dapat menemukan barang melalui pencarian dan filter.
- Transaksi masuk menambah stok dengan benar.
- Transaksi keluar mengurangi stok dan menolak jumlah yang melebihi stok tersedia.
- Riwayat transaksi menyimpan pengguna, waktu, tanggal, dan detail barang.
- Pengguna dapat melihat laporan berdasarkan rentang tanggal dan filter yang tersedia.
- Pengguna tanpa izin tidak dapat mengakses fungsi khusus Admin.

## 4. Pengguna dan hak akses
| Fitur | Admin | Staff |
|---|---:|---:|
| Dashboard | Ya | Ya |
| Melihat barang | Ya | Ya |
| Membuat/mengubah barang | Ya | Ya |
| Menghapus barang | Ya | Tidak |
| Mengelola kategori | Ya | Ya* |
| Mengelola lokasi | Ya | Ya* |
| Membuat barang masuk | Ya | Ya |
| Membuat barang keluar | Ya | Ya |
| Melihat riwayat transaksi | Ya | Ya |
| Melihat/mengunduh laporan | Ya | Ya |
| Manajemen pengguna | Ya | Tidak |
| Melihat activity log | Ya | Tidak |

`*` Hak akses kategori/lokasi dapat dibatasi ke Admin bila kebijakan operasional perusahaan mengharuskannya. Tetapkan sebelum implementasi final.

## 5. Ruang lingkup MVP
1. Autentikasi dan logout.
2. Dashboard ringkas.
3. CRUD kategori.
4. CRUD lokasi.
5. CRUD data barang dan pencarian/filter.
6. Pencatatan barang masuk dengan beberapa detail produk.
7. Pencatatan barang keluar dengan beberapa detail produk.
8. Riwayat transaksi.
9. Laporan stok saat ini dan laporan transaksi.
10. Manajemen akun pengguna oleh Admin.
11. Activity log untuk aktivitas penting.

## 6. Di luar ruang lingkup MVP
- Multi-company / SaaS.
- Stok satu produk yang tersebar pada beberapa gudang secara bersamaan.
- Purchase order dan procurement.
- Akuntansi dan pembukuan.
- Integrasi POS.
- Barcode/QR scanning.
- Aplikasi native Android/iOS.
- Persetujuan bertingkat.
- Manajemen peminjaman aset secara penuh.
- Forecasting dan demand planning.

## 7. Aturan bisnis
1. Setiap produk memiliki tepat satu lokasi penyimpanan aktif melalui `location_id`.
2. Kode produk harus unik.
3. Produk memiliki kategori, unit, stok minimum, dan status aktif.
4. Stok saat ini disimpan pada `products.stock`.
5. Barang masuk menambah stok.
6. Barang keluar mengurangi stok.
7. Kuantitas transaksi harus lebih besar dari nol.
8. Stok tidak boleh menjadi negatif.
9. Perubahan stok harus terjadi bersamaan dengan penyimpanan header dan detail transaksi.
10. Setiap transaksi mencatat pengguna pembuat.
11. Transaksi yang sudah diposting tidak diubah atau dihapus melalui alur CRUD biasa.
12. Produk yang sudah memiliki riwayat transaksi tidak boleh dihapus secara permanen. Nonaktifkan/arsipkan.
13. Stok awal dicatat sebagai transaksi barang masuk dengan sumber/keterangan "Saldo awal" yang dapat ditelusuri, bukan diketik bebas di form edit produk.
14. Produk nonaktif tidak dapat dipilih pada transaksi baru, tetapi tetap tampil dalam histori.

## 8. Kebutuhan fungsional
### FR-01 Autentikasi
Sistem menyediakan login dan logout. Password disimpan menggunakan hashing Laravel.

### FR-02 Dashboard
Menampilkan total jenis produk aktif, total unit stok, ringkasan barang masuk/keluar pada periode berjalan, daftar stok rendah, dan aktivitas terbaru.

### FR-03 Produk
Admin/Staff yang berwenang dapat menambah, melihat, dan mengubah produk. Hanya Admin dapat menghapus atau menonaktifkan sesuai aturan. Kolom stok tidak dapat diedit langsung.

### FR-04 Kategori dan lokasi
Pengguna berwenang dapat mengelola nama dan deskripsi kategori/lokasi. Data yang sudah direferensikan produk tidak boleh dihapus sembarangan.

### FR-05 Barang masuk
Pengguna membuat satu transaksi dengan satu atau lebih baris produk dan kuantitas. Sistem menyimpan header dan detail, kemudian menambah stok.

### FR-06 Barang keluar
Pengguna membuat satu transaksi dengan satu atau lebih baris produk dan kuantitas. Sistem memeriksa stok tersedia, menyimpan transaksi, lalu mengurangi stok secara atomik.

### FR-07 Riwayat
Sistem menampilkan kode transaksi, tanggal, tipe, pembuat, dan ringkasan detail. Pengguna dapat membuka detail transaksi.

### FR-08 Laporan
Sistem menampilkan stok saat ini dan laporan barang masuk/keluar dengan filter tanggal, kategori, lokasi, dan produk. Ekspor Excel/PDF merupakan fitur opsional sesuai waktu dan kebutuhan.

### FR-09 Pengguna
Admin dapat membuat akun, mengubah nama/role, dan menonaktifkan akun. Jangan izinkan Admin menonaktifkan dirinya sendiri jika itu menyebabkan tidak ada administrator aktif.

### FR-10 Activity log
Sistem mencatat aktivitas penting seperti login (opsional), pembuatan/perubahan produk, transaksi stok, perubahan pengguna, dan perubahan master data.

## 9. Kebutuhan nonfungsional
- Antarmuka responsif untuk desktop dan tablet.
- Validasi dilakukan di server.
- Password menggunakan hashing.
- Akses dibatasi berdasarkan role.
- Database menggunakan InnoDB dan utf8mb4.
- Query daftar menggunakan pagination.
- Pesan error mudah dimengerti.
- Kode mengikuti konvensi Laravel dan mudah dipelihara.
- Backup database perlu menjadi prosedur operasional saat deployment.

## 10. Asumsi dan pertanyaan terbuka
- Apakah kategori dan lokasi boleh dikelola Staff?
- Apakah harga beli/jual diperlukan untuk semua jenis barang, termasuk aset internal?
- Apakah perusahaan membutuhkan ekspor Excel, PDF, atau keduanya?
- Apakah kode transaksi memiliki format khusus yang sudah digunakan?
- Apakah transaksi memerlukan bukti/foto/dokumen?
- Siapa yang berwenang melakukan koreksi jika transaksi salah?
- Apakah stok minimum ditetapkan per produk?

Jawaban harus dikonfirmasi dengan pihak perusahaan.