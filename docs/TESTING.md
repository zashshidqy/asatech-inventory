# Testing Strategy

## Metode
Black Box Testing: pengujian berdasarkan input dan output/perilaku sistem tanpa memeriksa detail kode internal. Gunakan Feature Tests Laravel untuk mengotomatisasi kasus penting bila memungkinkan.

## Skenario inti
| ID | Fitur | Input/kondisi | Hasil yang diharapkan |
|---|---|---|---|
| TC-01 | Login | Email/password valid | Masuk ke dashboard |
| TC-02 | Login | Password salah | Login ditolak, pesan aman |
| TC-03 | Akun | Akun nonaktif | Login ditolak |
| TC-04 | Produk | Kode barang baru valid | Produk tersimpan |
| TC-05 | Produk | Kode barang duplikat | Validasi gagal |
| TC-06 | Produk | Stok diisi dari form edit | Field tidak tersedia/diabaikan dan stok tidak berubah |
| TC-07 | Kategori | Nama valid | Kategori tersimpan |
| TC-08 | Lokasi | Lokasi yang masih digunakan dihapus | Ditolak |
| TC-09 | Barang masuk | Stok awal 10, masuk 5 | Stok menjadi 15 |
| TC-10 | Barang keluar | Stok 15, keluar 4 | Stok menjadi 11 |
| TC-11 | Barang keluar | Stok 11, keluar 20 | Ditolak, stok tetap 11 |
| TC-12 | Transaksi | Dua baris produk valid | Header dan seluruh detail tersimpan |
| TC-13 | Transaksi | Salah satu detail gagal | Seluruh transaksi rollback |
| TC-14 | Otorisasi | Staff membuka URL manajemen user | 403/ditolak |
| TC-15 | Otorisasi | Staff menghapus produk | 403/ditolak |
| TC-16 | Riwayat | Membuka detail transaksi | Header dan detail tampil benar |
| TC-17 | Laporan | Filter tanggal/kategori | Data sesuai filter |
| TC-18 | Activity log | Transaksi berhasil | Log tercatat |

## Pengujian manual
Catat:
- ID pengujian
- tanggal
- versi aplikasi
- langkah
- hasil yang diharapkan
- hasil aktual
- status (Pass/Fail)
- bukti screenshot bila diperlukan

## Pengujian integritas stok
Lakukan pengujian transaksi gagal dan pastikan tidak ada perubahan parsial pada stok maupun tabel transaksi.