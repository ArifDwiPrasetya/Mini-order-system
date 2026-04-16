# Mini Order System - Backend

Repositori ini berisi _source code_ bagian Backend (REST API) dari proyek **Mini Order System**. Sistem ini dibangun dengan memanfaatkan fitur-fitur modern dari Laravel untuk mengelola produk dan transaksi pesanan.

## Fitur Utama

- **Framework:** Laravel 13 (PHP 8.3+)
- **Autentikasi Aman:** Menggunakan **Laravel Passport** (OAuth2 - Password Grant) untuk penerbitan dan pembaruan Token JWT (_Access & Refresh Tokens_).
- **Arsitektur Clean Code:**
    - _Controllers_ hanya bertugas menangani _request/response_ HTTP.
    - _Service Layer_ (`app/Services`) memuat inti dari _business logic_.
    - _Form Requests_ (`app/Http/Requests`) digunakan untuk validasi _input_ secara ketat.
- **Database Transactions:** Menjamin integritas data saat pembuatan order (pengurangan stok dan pencatatan item) menggunakan `DB::beginTransaction()`.
- **Kinerja Optimal (Concurrency):** Endpoint Dashboard menggunakan fitur `Concurrency::run()` untuk mengambil ringkasan data secara paralel.
- **Background Processing:** Menggunakan _Laravel Queue/Jobs_ untuk simulasi pengiriman notifikasi agar proses utama tidak terblokir (_async_).
- **Pagination & Search:** Dukungan bawaan pada _endpoint_ Produk dan Order.

---

## Prasyarat Sistem

Pastikan sistem telah terpasang perangkat lunak berikut:

- **PHP** (Versi 8.3 atau lebih baru)
- **Composer** (Versi 2.x)
- **MySQL Server** (Atau MariaDB)
- **Git**

---

## Panduan Instalasi & Setup

Ikuti langkah-langkah di bawah ini secara berurutan untuk menjalankan Backend di lingkungan lokal Anda.

### 1. Kloning Repositori & Instalasi Dependensi

Buka terminal Anda dan jalankan perintah berikut:

```bash
git clone <URL_REPOSITORY_ANDA>
cd Mini-order-system/backend
composer install
```

### 2. Konfigurasi Environment (.env)

Buat file .env dengan perintah berikut:

```bash
cp .env.example .env

```

Sesuaikan konfigurasi database di file .env dan tambahkan konfigurasi jika belum ada agar terlihat seperti ini :

DB_CONNECTION=mysql  
DB_HOST=127.0.0.1  
DB_PORT=3306  
DB_DATABASE=nama_database_anda  
DB_USERNAME=root  
DB_PASSWORD=

CONCURRENCY_DRIVER=sync  
PASSPORT_PASSWORD_CLIENT_ID= isi_dengan_mengikuti_lengkah_selanjutnya  
PASSPORT_PASSWORD_SECRET= isi_dengan_mengikuti_lengkah_selanjutnya

### 3. Generate Application Key, Migrasi, dan Konfigurasi Laravel Passport

Jalankan perintah ini secara bergantian :

```bash
php artisan key:generate
php artisan migrate --seed
php artisan passport:keys
php artisan passport:client --password
```

Setelah menjalankan perintah php artisan passport:client --passport, Masukkan :  
**Name:** Bebas (misal: Mini Order System)  
**Provider:** Pilih users dengan menginput angka yang ada di sudut kanan terminal baris users kemudian tekan enter.

Akan ada Client ID dan Secret ID yang tampil di terminal :  
PASSPORT_PASSWORD_CLIENT_ID=019d9418-a7a4-71da-8544-97a34c87cca2 # (Ini hanya contoh, gunakan ID Anda)  
PASSPORT_PASSWORD_SECRET=nwKfO6pFsYJhshK6di8SnV4kXzrmiKKjPBobmOnK # (Ini hanya contoh, gunakan Secret Anda)

Masukkan Client ID dan Secret ID yang anda peroleh ke dalam konfigurasi file .env

### 4. Running project

Setelah menyimpan file .env, bersihkan cache konfigurasi dan jalankan project laravel dengan memasukkan perintah-perintah berikut :

```bash
php artisan serve
```

### 5. Running Laravel Job

Buka terminal baru dan jalankan laravel job yang diperlukan dalam project ini dengan perintah berikut:

```bash
php artisan queue:work
```

### Credential Login

**email : admin@admin.com**  
**password : password123**
