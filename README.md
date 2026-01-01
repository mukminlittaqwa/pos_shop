# Manajemen Toko - REST API (Laravel 11 + JWT)

API Backend untuk sistem **Manajemen Toko** sesuai technical test PT. Dazo Kreatif Indonesia.

![Laravel](https://img.shields.io/badge/Laravel-11-red?logo=laravel)
![JWT](https://img.shields.io/badge/JWT-Auth-black)
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.2-orange)

## Fitur Utama

-   **Multi Level Toko**: Pusat, Cabang, Retail
-   **Super Admin** dapat membuat dan mengelola toko
-   **Auto Generate User**: Setiap toko baru otomatis dibuat 1 Admin + 1 Kasir
-   **Role & Permission** menggunakan Spatie Laravel Permission
-   **Admin Toko** dapat:
    -   CRUD Produk
    -   CRUD Kasir tambahan di tokonya sendiri
    -   Melihat semua penjualan
    -   Mengubah profile diri sendiri
-   **Kasir** dapat:
    -   Melihat detail produk
    -   Melakukan penjualan (cart sederhana + pembayaran)
    -   Melihat penjualan
    -   Mengubah profile diri sendiri
-   **Data Scoping**: Setiap user hanya melihat data toko miliknya (kecuali Super Admin)
-   **Authentication**: JWT (stateless)
-   **Pagination & Search** di semua list data
-   **Error Handling** JSON yang rapi (401 Unauthenticated, 403 Unauthorized, dll)

## Tech Stack

-   Laravel 11
-   MySQL
-   JWT Authentication (`php-open-source-saver/jwt-auth`)
-   Spatie Laravel Permission v6
-   PHP >= 8.2

## Requirements

-   PHP >= 8.2
-   Composer
-   MySQL
-   Git

## Installation

### 1. Clone repository

```bash
git clone https://github.com/USERNAME/manajemen-toko-api.git
cd manajemen-toko-api
```

### 2. Install dependencies

```bash
composer install
```

### 3. Copy & konfigurasi .env

```bash
Copy & konfigurasi .env
```

```bash
Edit .env untuk database:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_shop
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate key & JWT secret

```bash
php artisan key:generate
php artisan jwt:secret
```

### 5. Migrate database + seed data test

```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan server

```bash
php artisan serve
```

API berjalan di http://localhost:8000/api

Database & ERDLihat file ERD_Manajemen_Toko.png atau ERD_Manajemen_Toko.pdf di root repository untuk diagram lengkap.Tabel Utama:users
shops
products
sales
sale_items
payments
Tabel Spatie: roles, permissions, model_has_roles, dll

Catatan Tambahan

-   Produk tidak memiliki stok (unlimited quantity)
-   Pembayaran sederhana (cash dengan kembalian otomatis)
-   Semua response JSON dengan struktur rapi
-   Error handling lengkap & aman
-   Data scoped per toko menggunakan Global Scope

## API Endpoints

| Method     | Endpoint         | Description                                  | Role Required | Request Body Example                                                                                        | Response Example        |
| ---------- | ---------------- | -------------------------------------------- | ------------- | ----------------------------------------------------------------------------------------------------------- | ----------------------- |
| **POST**   | `/login`         | Login & dapatkan JWT token                   | Public        | `json { "email": "super@dazo.com", "password": "password" } `                                               | Token + user data       |
| **GET**    | `/me`            | Lihat profile diri + role                    | Authenticated | -                                                                                                           | User + roles            |
| **PUT**    | `/profile`       | Update profile diri (name, email, password)  | Authenticated | `json { "name": "Nama Baru", "password": "baru123" } `                                                      | Profile updated         |
| **POST**   | `/logout`        | Logout (invalidate token)                    | Authenticated | -                                                                                                           | Logged out              |
| **GET**    | `/shops`         | List toko (hanya super admin lihat semua)    | Super Admin   | -                                                                                                           | List shops              |
| **POST**   | `/shops`         | Buat toko baru + auto generate admin & kasir | Super Admin   | `json { "name": "Toko Baru", "level": "cabang" } `                                                          | Shop + credentials      |
| **GET**    | `/products`      | List produk di toko sendiri                  | Admin & Kasir | ?search=Indomie                                                                                             | Paginated products      |
| **POST**   | `/products`      | Tambah produk                                | Admin Toko    | `json { "name": "Indomie", "description": "...", "price": 3500 } `                                          | Product created         |
| **PUT**    | `/products/{id}` | Update produk                                | Admin Toko    | `json { "price": 4000 } `                                                                                   | Product updated         |
| **DELETE** | `/products/{id}` | Hapus produk                                 | Admin Toko    | -                                                                                                           | Deleted                 |
| **GET**    | `/cashiers`      | List kasir di toko sendiri                   | Admin Toko    | ?search=kasir                                                                                               | Paginated cashiers      |
| **POST**   | `/cashiers`      | Tambah kasir baru                            | Admin Toko    | `json { "name": "Kasir Baru", "email": "email@toko.com", "password": "123456" } `                           | Kasir + password        |
| **PUT**    | `/cashiers/{id}` | Update kasir lain                            | Admin Toko    | `json { "name": "Nama Baru" } `                                                                             | Updated                 |
| **DELETE** | `/cashiers/{id}` | Hapus kasir lain                             | Admin Toko    | -                                                                                                           | Deleted                 |
| **GET**    | `/sales`         | List penjualan di toko sendiri               | Admin & Kasir | ?search=1                                                                                                   | Paginated sales         |
| **POST**   | `/sales`         | Buat penjualan baru (cart + payment)         | Admin & Kasir | `json { "items": [ { "product_id": 1, "quantity": 3 } ], "payment_method": "cash", "amount_paid": 50000 } ` | Sale detail + kembalian |
| **GET**    | `/sales/{id}`    | Detail penjualan + items + payment           | Admin & Kasir | -                                                                                                           | Sale detail             |

**Catatan:**

-   Semua endpoint kecuali `/login` memerlukan header:  
    `Authorization: Bearer [JWT_TOKEN]`  
    `Accept: application/json`  
    `Content-Type: application/json`
-   Error response selalu JSON (401 Unauthenticated, 403 Unauthorized, 422 Validation, dll)
