# Point of Sale (PoS)

A full-featured Point of Sale application built with Laravel 11, Vue 3, Inertia.js, and Tailwind CSS.

![PoS Screen](PoS%20screen.png)

## Features

- **Multi-store support** — manage multiple stores with independent products, categories, and sales
- **PIN-based authentication** — simple email + 4-digit PIN login for cashiers and admins
- **POS Terminal** — product grid with category filters, search, cart management, and checkout
- **Product & Category Management** — full CRUD with search and filtering
- **Sales History** — browse past sales with date, cashier, and payment method filters
- **Reports & Charts** — revenue over time, sales by team member, payment method breakdown (Chart.js)
- **User & Store Management** — admin-only CRUD for users and stores

## Tech Stack

- **Backend:** Laravel 11, PHP 8.4, SQLite
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS v4 (compiled via Vite)
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
