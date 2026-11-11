# AsaTech Inventory

Aplikasi web internal untuk manajemen inventaris barang pada AsaTech.

## Tech Stack

- **Laravel 13** - PHP Framework
- **PHP 8.4** - Programming Language
- **Tailwind CSS 4** - Styling
- **Vite** - Asset Bundler
- **SQLite** - Database (Development)
- **MySQL/MariaDB** - Database (Production Ready)

## Features

### Phase 1 - Foundation ✅ (Completed)
- Authentication (Login/Logout)
- Role-based Access Control (Admin/Staff)
- Dashboard Layout
- Database Schema (10 tables)
- Eloquent Models with Relationships

### Phase 2 - Master Data (Next)
- CRUD Categories
- CRUD Locations
- CRUD Products
- Search, Filter, Pagination

### Phase 3 - Inventory Transactions
- Stock In (with dynamic rows)
- Stock Out (with atomic updates)
- Transaction History
- Activity Logs

### Phase 4-5 - Reporting
- Current Stock Report
- Stock Movement Report
- Export (optional)

### Phase 6 - Testing & Documentation
- Black Box Testing
- User Guide
- Deployment Guide

## Installation

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js & npm
- SQLite (dev) or MySQL/MariaDB (production)

### Setup

```bash
# Clone repository
git clone https://github.com/zashshidqy/asatech-inventory.git
cd asatech-inventory

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Build assets
npm run build

# Start development server
php artisan serve
```

### Development Users

After running seeders, you can login with:

**Admin:**
- Email: `admin@asatech.test`
- Password: `password123`

**Staff:**
- Email: `staff@asatech.test`
- Password: `password123`

## Database Schema

- `users` - User accounts with roles
- `categories` - Product categories
- `locations` - Storage locations
- `products` - Product master data with stock
- `stock_ins` - Stock in transaction headers
- `stock_in_items` - Stock in transaction details
- `stock_outs` - Stock out transaction headers
- `stock_out_items` - Stock out transaction details
- `activity_logs` - System activity logs

## Documentation

Complete documentation available in `/docs`:

- `PRD.md` - Product Requirements Document
- `ARCHITECTURE.md` - System Architecture
- `DATABASE.md` - Database Specification
- `ERD.md` - Entity Relationship Diagram
- `IMPLEMENTATION_PLAN.md` - Development Roadmap
- `FEATURE_SPECIFICATION.md` - Feature Details
- `UI_UX_GUIDELINES.md` - UI/UX Guidelines
- `SECURITY.md` - Security Guidelines
- `TESTING.md` - Testing Strategy
- `TODO.md` - Project Progress
- `CHANGELOG.md` - Change History

## Development Guidelines

See `AGENTS.md` for development principles and coding standards.

## Git Workflow

```bash
# To push updates to GitHub, you'll need to authenticate
git push -u origin master

# Or use SSH if configured
git remote set-url origin git@github.com:zashshidqy/asatech-inventory.git
git push -u origin master
```

## Production Deployment

### Database Configuration

For production, update `.env` to use MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asatech_inventory
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Security Checklist

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Use strong `APP_KEY`
- Enable HTTPS
- Secure database credentials
- Configure backup strategy
- Review `docs/SECURITY.md`

## License

Internal use only - AsaTech.

## Repository

https://github.com/zashshidqy/asatech-inventory

---

**Status:** Phase 1 Complete | Next: Phase 2 - Master Data CRUD