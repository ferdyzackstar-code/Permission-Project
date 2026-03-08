# Permission Project

Application dashboard management built with Laravel for managing users, roles, product categories, and products. This project uses Spatie Permission for access control and Yajra DataTables for fast, interactive data tables.

## Overview

This project is intended as an admin panel foundation with these main functions:

-   user authentication with Laravel UI
-   user management
-   role management and permission-based access control
-   product category management
-   product management
-   server-side data tables using Yajra DataTables

## Main Features

### Authentication

-   login and logout
-   route protection with `auth` middleware

### Access Control

-   uses package `spatie/laravel-permission`
-   role and permission assignment to users
-   permission validation on controller actions

### Admin Modules

-   dashboard
-   user management
-   role management
-   product category management
-   product management

### Data Table Integration

-   server-side DataTables on selected modules
-   faster search, sorting, and pagination for large datasets

## Tech Stack

### Backend

-   PHP 8.2+
-   Laravel 12

### Frontend

-   Blade
-   Bootstrap
-   Vite
-   jQuery
-   DataTables

### Main Packages

-   `spatie/laravel-permission`
-   `yajra/laravel-datatables-oracle`
-   `laravel/ui`

## Project Structure

Important directories used in this project:

-   [app/Http/Controllers](app/Http/Controllers) — application controllers
-   [app/Models](app/Models) — Eloquent models
-   [resources/views](resources/views) — Blade views
-   [routes/web.php](routes/web.php) — web routes
-   [database/migrations](database/migrations) — database structure
-   [database/seeders](database/seeders) — initial data seeders

## System Requirements

Make sure your local environment meets these requirements:

-   PHP >= 8.2
-   Composer
-   Node.js and npm
-   MySQL/MariaDB
-   Web server or local stack such as Laragon

## Installation Guide

Follow these steps in order.

### 1. Clone project

Clone the repository into your local environment.

### 2. Enter project directory

Move into the project folder:

-   project folder: [Permission-Project](.)

### 3. Install PHP dependencies

Install all backend dependencies with Composer.

### 4. Install JavaScript dependencies

Install frontend dependencies with npm.

### 5. Create environment file

Copy `.env.example` to `.env`.

### 6. Configure database

Open the `.env` file and adjust the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=permission_project
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the values to match your local environment.

### 7. Generate application key

Generate the Laravel application key.

### 8. Run migrations

Create all required database tables.

### 9. Run seeders

This project provides seeders for permissions and admin user. Run them in this order:

1. `PermissionTableSeeder`
2. `CreateAdminUserSeeder`

If needed, you can also run the default `DatabaseSeeder` for sample user data.

### 10. Build frontend assets

Compile CSS and JavaScript assets with Vite.

### 11. Start development server

Run the Laravel development server and Vite dev server.

## Quick Setup Commands

Use the following command sequence for a fresh installation:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=PermissionTableSeeder
php artisan db:seed --class=CreateAdminUserSeeder
npm install
npm run build
php artisan serve
```

For frontend development mode:

```bash
npm run dev
```

## Default Admin Account

If you run `CreateAdminUserSeeder`, the default admin account is:

-   email: `admin@gmail.com`
-   password: `123456`

It is strongly recommended to change the password immediately after first login.

## Available Routes

Main application routes:

-   `/` — landing page
-   `/login` — login page
-   `/dashboard` — admin dashboard
-   `/dashboard/users` — user management
-   `/dashboard/roles` — role management
-   `/dashboard/products` — product management
-   `/dashboard/categories` — category management

Route definitions can be seen in [routes/web.php](routes/web.php).

## Step-by-Step Application Flow

### Step 1 — Login

Open the login page and sign in with an authorized account.

### Step 2 — Open Dashboard

After login, you will be redirected to the admin dashboard.

### Step 3 — Manage Users

Use the user module to:

-   add user
-   edit user
-   delete user
-   assign role to user

### Step 4 — Manage Roles

Use the role module to:

-   create role
-   update role
-   assign permissions to role

### Step 5 — Manage Categories

Use the category module to organize products by category.

### Step 6 — Manage Products

Use the product module to:

-   add product
-   set category
-   set branch name
-   fill product detail

### Step 7 — Use DataTables

On modules that already use Yajra DataTables, you can:

-   search data quickly
-   sort columns
-   view paginated records efficiently

## Permissions and Seeder Notes

Current permission seed data is defined in [database/seeders/PermissionTableSeeder.php](database/seeders/PermissionTableSeeder.php).

Seeded permissions currently include:

-   `role-list`
-   `role-create`
-   `role-edit`
-   `role-delete`
-   `product-list`
-   `product-create`
-   `product-edit`
-   `product-delete`

If you add new modules such as permission CRUD or extra user permissions, update the seeder accordingly.

## Useful Development Commands

### Run local server

```bash
php artisan serve
```

### Run Vite in development mode

```bash
npm run dev
```

### Build production assets

```bash
npm run build
```

### Run tests

```bash
php artisan test
```

### Clear application cache

```bash
php artisan optimize:clear
```

## Troubleshooting

### 1. Database connection failed

Check `.env` and make sure `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` are correct.

### 2. Assets not loaded

Run:

```bash
npm install
npm run build
```

or use:

```bash
npm run dev
```

### 3. Permission/role feature not working

Make sure these steps have been run:

-   migration completed successfully
-   `PermissionTableSeeder` executed
-   `CreateAdminUserSeeder` executed

### 4. Storage or cache issues

Run:

```bash
php artisan optimize:clear
```

## Development Notes

Current improvement ideas:

-   add full permission CRUD interface
-   standardize all modules to use Yajra DataTables
-   improve authentication views and dashboard UX

## License

This project follows the Laravel ecosystem license standard and can be further adjusted according to your team or company needs.
