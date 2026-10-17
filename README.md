# Yazh Creations

A production-ready Laravel 13 product-showcase website for **Yazh Creations** — a women's fashion and
handcrafted accessories brand. Visitors can browse the catalogue, enquire about products via the
enquiry form or directly through WhatsApp, while an admin-only dashboard manages products,
categories, images, enquiries and site settings.

## Tech Stack

- **Laravel 13** (PHP 8.3) + Blade + Vite
- **PostgreSQL** (main + test databases)
- **Bootstrap 5** with Bootstrap Icons (Tailwind removed)
- Custom brand theme: cream `#F8F1E7`, burgundy `#6E173A`, gold `#B88A3B`, dark `#2B2522`
- GD-generated placeholder images and brand assets

## Features

**Public site**
- Home (hero, shop by category, new arrivals, featured products, why-us, about, Instagram, WhatsApp CTA)
- Product listing with search, category filter, sorting, and "featured only" toggle
- Product detail with image gallery, size-aware WhatsApp CTA, enquiry form and related products
- Category pages (404 for inactive / product-less categories)
- About and Contact pages, custom error pages (403/404/419/429/500)
- `sitemap.xml` (products + categories, noindex)

**Enquiry flow**
- Products stored with sizes, prices, discounts, stock status and material/colour info
- Enquiry form (name, phone, size, message) saved to the database
- Every enquiry also builds a pre-filled WhatsApp message; the page offers a "Chat on WhatsApp" button

**Admin dashboard** (`/admin`, custom auth)
- Login, logout, forgot / reset password (password reset via Mailpit locally)
- Products: full CRUD, image upload + primary image control, soft-delete trash / restore / force delete
- Categories: CRUD with safe-delete protection when products exist
- Enquiries: list with search + status + product filters, detail view, status workflow (new / contacted / closed)
- Settings: brand name, tagline, WhatsApp number, social links, contact info, hero + about text

## Requirements

- PHP 8.3+ with `pdo_pgsql` and `pgsql` extensions enabled
- PostgreSQL 16 (Laragon portable install works)
- Composer 2 and Node 20+

## Installation

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure .env for PostgreSQL
#    DB_CONNECTION=pgsql
#    DB_HOST=127.0.0.1
#    DB_PORT=5432
#    DB_DATABASE=yazh_creations
#    DB_USERNAME=postgres
#    DB_PASSWORD=postgres
#    FILESYSTEM_DISK=public
#    MAIL_MAILER=smtp  MAIL_HOST=127.0.0.1  MAIL_PORT=1025   # Mailpit
#    ADMIN_NAME / ADMIN_EMAIL / ADMIN_PASSWORD  (default admin user)

# 4. Create the databases and migrate + seed
createdb --user postgres yazh_creations
createdb --user postgres yazh_creations_test   # used by phpunit.xml
php artisan migrate --seed

# 5. Public storage symlink + assets
php artisan storage:link
npm run build          # or npm run dev while developing

# 6. Serve
php artisan serve      # http://127.0.0.1:8000
```

### Laragon

The project lives under `C:\laragon\www\yazh-creations`. Run **Start All** in Laragon, then add a
site for the project (e.g. `yazh-creations-test`) or use `php artisan serve` from the project folder.

## Default Admin

| Field    | Value                    |
| -------- | ------------------------ |
| Email    | `admin@yazhcreations.com` |
| Password | `password`               |

Login at `/admin/login`. Override via `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`.

> Change the default password after your first login.

## Seeded Content

Running `php artisan db:seed` (included in `migrate --seed`) creates:

- An admin user (from `ADMIN_*` env vars)
- 10 business settings (brand name, tagline, WhatsApp number, Instagram, contact info, hero/about text)
- 5 categories and 13 products (3 GD-generated images each)
- Placeholder brand assets: `public/images/branding/logo.png`, `placeholder-product.jpg`, `placeholder-category.jpg`

## Testing

```bash
php artisan test
```

The suite (51 feature tests) covers public pages, the enquiry flow, admin auth, product/category
CRUD, trash/restore, enquiries and settings. Tests run against `yazh_creations_test` (PostgreSQL).

## Code Style

```bash
vendor/bin/pint          # format changed files
npm run build             # production assets
```