# Security and Access Control

## Authentication
- Semua halaman operasional memerlukan login.
- Gunakan hashing password bawaan Laravel.
- Regenerate session setelah login dan logout dengan mekanisme Laravel.
- Akun `is_active = false` tidak boleh melakukan login.
- Gunakan CSRF protection bawaan Laravel untuk form web.

## Authorization
- Role minimum: `admin` dan `staff`.
- Lindungi route dan aksi controller di server.
- Staff tidak boleh mengakses manajemen pengguna atau aksi hapus produk.
- Jangan mengandalkan conditional Blade seperti `@if($user->is_admin)` sebagai satu-satunya proteksi.
- Gunakan middleware/Policy/Gate dan uji akses langsung melalui URL.

## Validation
- Validasi semua input di server.
- Gunakan allowlist untuk role, item_type, dan filter.
- Batasi panjang string dan tipe file jika kelak upload diaktifkan.
- Kuantitas transaksi harus integer dan minimal 1.
- Validasi product ID harus merujuk produk aktif.
- Jangan percaya harga atau stok dari browser; ambil data otoritatif dari database.

## Data integrity
- Gunakan foreign key.
- Gunakan transaction dan row locking pada perubahan stok.
- Hindari hard delete data yang sudah memiliki histori.
- Jangan pernah menerima `user_id` transaksi dari input pengguna.

## Deployment
- `APP_DEBUG=false` di production.
- HTTPS.
- Rahasiakan `.env` dan kredensial database.
- Jangan commit secret/API key ke Git.
- Batasi akses database dan lakukan backup berkala.