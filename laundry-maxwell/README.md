# Laundry Maxwell – Walk-in & Lacak Status (Laravel 12 + MySQL + Tailwind)

Paket ini berisi **overlay kode kustom** yang ditimpa ke proyek Laravel 12 + Laravel Breeze (Blade/Tailwind).

## Instalasi cepat
Prasyarat: PHP ≥ 8.2, Composer, Node.js, MySQL.

```bash
unzip laundry-maxwell.zip && cd laundry-maxwell
bash setup.sh laundry-app        # membuat proyek Laravel + Breeze + menyalin kode
cd laundry-app
# buat database MySQL "laundry" (atur DB_* di .env bila perlu)
php artisan migrate:fresh --seed
npm install && npm run dev       # terminal 1
php artisan serve                # terminal 2  -> http://127.0.0.1:8000
```

### Instalasi manual
1. `composer create-project laravel/laravel laundry-app`
2. `composer require laravel/breeze --dev && php artisan breeze:install blade`
3. Salin isi folder `src/` ke root proyek (timpa file yang sama).
4. Atur `.env` (MySQL), lalu `php artisan migrate:fresh --seed`.

> **Penting:** Breeze menyertakan fitur Register publik. Karena pelanggan didaftarkan oleh kasir,
> hapus route `register` (GET & POST) di `routes/auth.php`, dan hapus tes registrasi di `tests/Feature/Auth`.

## Akun & data demo (dari seeder)
| Peran | Login | Password |
|---|---|---|
| Admin/Kasir | `admin@laundry.test` atau `081200000000` | `password` |
| Pelanggan demo | `081234567890` / `budi@example.test` | `081234567890` |
| Resi demo | `LDR-DEMO-0001` (status: Disetrika) | – |

## Alur
- **Publik** `/` : landing page (Home, Layanan, Harga/Katalog, Tentang, Keunggulan, Blog, Kontak) + form **Cek Status** (No. Resi / No. HP) → `/lacak`.
- **Kasir** `/admin/orders` : order baru (daftarkan pelanggan otomatis berdasarkan No. HP), cetak nota thermal 58mm, ubah status (Diterima → Dicuci → Pengeringan → Disetrika → Siap Diambil → Sudah Diambil), tandai lunas.
- **Pelanggan** `/akun` : login No. HP / Email → riwayat transaksi + stepper progres.

## Kustomisasi
- Info outlet (alamat, telepon, WA, email, jam): `.env` → `LAUNDRY_NAME`, `LAUNDRY_ADDRESS`, `LAUNDRY_PHONE`, `LAUNDRY_WHATSAPP`, `LAUNDRY_EMAIL`, `LAUNDRY_HOURS` (default di `config/laundry.php`).
- Katalog harga: ubah di `database/seeders/DatabaseSeeder.php` (atau tambah CRUD `services`).
- Status/tahapan: konstanta `Order::STEPS` di `app/Models/Order.php`.

## Struktur file
```
bootstrap/app.php                      alias middleware `role`
config/laundry.php                     info outlet
routes/web.php                         publik, admin, pelanggan
app/Http/Middleware/RoleMiddleware.php
app/Http/Requests/Auth/LoginRequest.php   login No. HP / Email
app/Http/Controllers/{HomeController,TrackingController}.php
app/Http/Controllers/Admin/{OrderController,CustomerController}.php
app/Http/Controllers/Customer/DashboardController.php
app/Models/{User,Service,Order,OrderStatus}.php
database/migrations/  users, services, orders, order_statuses
database/seeders/DatabaseSeeder.php, database/factories/UserFactory.php
resources/views/ layouts/public, home, tracking/{show,list}, auth/login,
                 admin/orders/{index,create,show,nota}, customer/dashboard, partials/flash
```

## Catatan keamanan
- Lacak publik: nama disamarkan, tanpa harga/alamat, dengan `throttle` 20 req/menit.
- Password awal pelanggan = No. HP (praktis tapi lemah) → pertimbangkan OTP WhatsApp / tautan atur password.
- Satu order = satu layanan. Untuk multi-item per nota, tambahkan tabel `order_items`.
