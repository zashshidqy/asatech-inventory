# Changelog

Semua perubahan proyek dicatat dari yang terbaru.

## Unreleased
- Phase 3 Inventory Transactions completed:
  - StockInController with DB transaction + lockForUpdate() for atomic stock increment
  - StockOutController with DB transaction + lockForUpdate() + consistent ordering to prevent deadlock
  - Dynamic form rows (add/remove items) with product selection
  - Live stock display per product in dropdown
  - Transaction code generation: IN-YYYYMMDD-XXXX / OUT-YYYYMMDD-XXXX
  - Server-side validation with error display for dynamic rows
  - Duplicate product prevention in single transaction
  - Stock validation for Stock Out (prevent negative stock)
  - TransactionHistoryController: unified list with filters (type, date range, search), pagination
  - Detail views for Stock In & Stock Out with item breakdown
  - ActivityLogController: filterable log viewer (action, user, date range, search)
  - Added CategorySeeder, LocationSeeder, ProductSeeder with demo data
  - Updated DatabaseSeeder to include master data seeders

- Phase 2 Master Data CRUD completed:
  - Categories CRUD (create, read, update, delete with search, pagination, product count)
  - Locations CRUD (create, read, update, delete with search, pagination, product count)
  - Products CRUD (create, read, update, delete with search, filters, pagination)
  - Search & filter: category, location, active status, stock status
  - Stock field protected from direct edit (read-only)
  - Soft delete for products with transaction history
  - Transaction history display on product detail

## v0.1.0 - 2026-11-11
- Phase 1 Foundation complete:
  - Laravel 13 project initialized
  - Database migrations for all 10 tables (users, categories, locations, products, stock_ins, stock_in_items, stock_outs, stock_out_items, activity_logs)
  - Eloquent models with relationships
  - Authentication system (login/logout) with role-based access (admin/staff)
  - Dashboard layout with sidebar navigation
  - Tailwind CSS 4 + Vite configured
  - Database seeders for admin and staff development users
- Documentation baseline (PRD, ERD, DATABASE, ARCHITECTURE, IMPLEMENTATION_PLAN, FEATURE_SPECIFICATION, UI_UX_GUIDELINES, SECURITY, TESTING, DECISIONS, TODO, CHANGELOG, INTERNSHIP_REPORT)