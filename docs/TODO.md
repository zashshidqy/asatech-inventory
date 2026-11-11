# Project TODO

Status awal seluruh item perlu diverifikasi terhadap repository aktual.

## Audit
- [x] Audit struktur proyek dan versi dependency.
- [x] Audit autentikasi yang sudah ada.
- [x] Audit migration/model/controller yang sudah ada.

## Fondasi (Phase 1) - SELESAI
- [x] MySQL connection (using SQLite for dev, MySQL config ready)
- [x] Authentication (login/logout)
- [x] Admin/Staff authorization
- [x] Dashboard layout
- [x] Database migrations (10 tables)
- [x] Eloquent models (9 models) with relationships
- [x] Tailwind CSS 4 + Vite setup
- [x] Development seeders (admin & staff)

## Master data (Phase 2) - SELESAI
- [x] Categories CRUD
- [x] Locations CRUD
- [x] Products CRUD (tanpa edit stok)
- [x] Search, filter, pagination untuk master data

## Inventory transactions (Phase 3) - IN PROGRESS
- [x] Stock in form dengan dynamic rows
- [x] Stock out form dengan dynamic rows
- [x] Pembuatan kode transaksi yang aman (IN-YYYYMMDD-XXXX / OUT-YYYYMMDD-XXXX)
- [x] DB transaction dan lockForUpdate untuk Stock In
- [x] DB transaction dan lockForUpdate untuk Stock Out (dengan ordering untuk prevent deadlock)
- [x] Validasi stok dan rollback
- [ ] Transaction history
- [ ] Activity logs viewer

## Reporting (Phase 4-5)
- [ ] Current stock report
- [ ] Stock movement report
- [ ] Export (optional)

## Finalization (Phase 6)
- [ ] Black Box Testing
- [ ] User guide
- [ ] Deployment guide
- [ ] Internship report evidence