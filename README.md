# Point of Sale (PoS)

A full-featured Point of Sale application built with Laravel 11, Vue 3, Inertia.js, and Tailwind CSS.

![PoS Screen](PoS%20screen%20v2.png)

## Features

- **POS Terminal** — product grid with category filters, search, quantity controls (+/-), and cart management
- **Order Types** — Dine-in, Takeaway, and Delivery with optional customer name and table number
- **Multi-store support** — manage multiple stores with independent products, categories, and sales; admins can switch stores on the fly
- **PIN-based authentication** — email + 4-digit PIN login with "Remember me" support
- **Collapsible sidebar** — full navigation with icons-only collapsed mode (persisted to localStorage), sub-menus, and tooltips
- **Mobile-responsive** — dedicated mobile view toggle (Menu/Cart) for the POS terminal, horizontal-scroll tables, and adaptive layouts
- **Product & Category Management** — full CRUD with inline category creation from the product form
- **Sales History** — sortable columns (Sale #, Date, Total), filterable by date range, cashier, payment method, store, and order type
- **Reports & Charts** — revenue over time, sales by team member, payment method breakdown, sales by store (Chart.js)
- **User & Store Management** — admin-only CRUD for users and stores
- **Built-in Calculator** — modal calculator accessible from the sidebar with full keyboard support

## Tech Stack

- **Backend:** Laravel 11, PHP 8.4, SQLite
- **Frontend:** Vue 3 (Composition API), Inertia.js, Tailwind CSS v4 (compiled via Vite)
- **Charts:** Chart.js via vue-chartjs

## Getting Started

```bash
# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations and seed demo data
php artisan migrate:fresh --seed

# Start the app
php artisan serve   # Backend at localhost:8000
npm run dev         # Vite dev server (separate terminal)
```

## Demo Credentials

| Role    | Email          | PIN  |
|---------|----------------|------|
| Admin   | admin@pos.com  | 1234 |
| Cashier | jane@pos.com   | 1234 |
| Cashier | bob@pos.com    | 1234 |
| Admin   | alice@pos.com  | 1234 |

## License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
