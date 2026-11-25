# Praktikum Laravel

Kumpulan proyek latihan (praktikum) **Laravel** yang terdiri dari dua aplikasi terpisah. Masing-masing folder adalah project Laravel sendiri dengan `composer.json`, `.env`, dan database sendiri.

| Folder | Isi singkat |
| --- | --- |
| [`Laravel-Admin`](./Laravel-Admin) | Dashboard admin dengan template SB Admin 2, login session, dan CRUD data admin |
| [`MahasiswaController`](./MahasiswaController) | Latihan controller dan view: daftar mahasiswa dan kalkulator luas segitiga |

## Teknologi

- PHP ^8.3
- Laravel ^13.17
- Blade sebagai template engine
- Vite dan Tailwind CSS 4 (bawaan skeleton Laravel)
- Database: SQLite (default `.env.example`) atau MySQL

## Struktur Repositori

```
Praktikum-Laravel/
├── Laravel-Admin/
│   ├── app/Http/Controllers/    # AuthController, AdminController, DashboardController
│   ├── app/Http/Middleware/     # IsLogin
│   ├── app/Models/              # Admin, User
│   ├── resources/views/         # login, dashboard, admin/*, template/*
│   ├── public/sb-admin-2/       # aset template SB Admin 2
│   └── routes/web.php
└── MahasiswaController/
    ├── app/Http/Controllers/    # MahasiswaController, KalkulatorController
    ├── resources/views/         # layouts/app, mahasiswa, segitiga
    ├── public/css/style.css     # styling kustom
    └── routes/web.php
```

## Persiapan Umum

Prasyarat untuk kedua proyek:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm (hanya jika memakai Vite)
- MySQL (misalnya lewat Laragon), jika tidak memakai SQLite

Clone repositori:

```bash
git clone https://github.com/<username>/Praktikum-Laravel.git
cd Praktikum-Laravel
```

Langkah dasar untuk setiap proyek (ganti `<folder>` dengan `Laravel-Admin` atau `MahasiswaController`):

```bash
cd <folder>
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Aplikasi berjalan di `http://localhost:8000`. Jika dua proyek dijalankan bersamaan, beri port berbeda, misalnya `php artisan serve --port=8001`.

---

## 1. MahasiswaController

Latihan dasar routing, controller, dan view Blade. Fitur proyek ini **tidak memakai database**, sehingga bisa langsung dijalankan tanpa migrasi khusus.

### Fitur

- **Daftar Mahasiswa:** menampilkan tabel mahasiswa (nama, alamat, gender) dari array di `MahasiswaController`.
- **Kalkulator Luas Segitiga:** form `alas` dan `tinggi` yang mengirim data lewat query string (GET), divalidasi, lalu menampilkan hasil luas = ½ × alas × tinggi.
- Layout Blade bersama (`layouts/app.blade.php`) dengan navbar, dan styling kustom di `public/css/style.css`.

### Routing

| Method | URL | Controller | Nama route |
| --- | --- | --- | --- |
| GET | `/` | Halaman `welcome` | - |
| GET | `/daftar/mahasiswa` | `MahasiswaController@index` | `mahasiswa.index` |
| GET | `/kalkulator/segitiga` | `KalkulatorController@segitiga` | `kalkulator.segitiga` |

### Contoh Penggunaan

- Daftar mahasiswa: `http://localhost:8000/daftar/mahasiswa`
- Kalkulator: `http://localhost:8000/kalkulator/segitiga?alas=10&tinggi=5` menghasilkan luas **25**.

### Validasi

- `alas`: wajib, numerik, minimal 0
- `tinggi`: wajib, numerik, minimal 0

Pesan error ditampilkan di atas hasil perhitungan.

### Menjalankan

```bash
cd MahasiswaController
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

---

## 2. Laravel-Admin

Dashboard admin berbasis template **SB Admin 2** dengan login session dan CRUD data admin.

### Fitur

- Login dengan email dan password (session-based)
- Middleware `IsLogin` untuk melindungi halaman dashboard
- Halaman Home, Product, Service, Contact, dan About
- CRUD data admin (tambah, lihat, edit, hapus) dengan validasi form
- Password di-hash otomatis lewat cast `hashed` pada model
- Layout Blade terpisah: header, sidebar, footer

### Routing

| Method | URL | Keterangan | Perlu login |
| --- | --- | --- | --- |
| GET | `/` | Redirect ke `/home` | - |
| GET | `/login` | Form login | Tidak |
| POST | `/login` | Proses login | Tidak |
| GET | `/home`, `/product`, `/service`, `/contact`, `/about` | Halaman dashboard | Ya |
| Resource | `/admin` | CRUD admin (index, create, store, edit, update, destroy) | Ya |
| POST | `/logout` | Logout | Ya |

### Menjalankan

```bash
cd Laravel-Admin
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Untuk memakai **MySQL**, ubah bagian database di `.env` dan buat database `laravel_admin` terlebih dahulu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_admin
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan migrasi dan server:

```bash
php artisan migrate
php artisan serve
```

> **Penting:** kode Laravel-Admin memakai tabel dan kolom yang **belum dibuat oleh migrasi bawaan**:
>
> - Tabel `admins` (kolom `id_admin`, `username`, `password`, `created_at`, `updated_at`) belum ada di migrasi.
> - Tabel `users` bawaan memiliki kolom `name`, sedangkan `AuthController` memakai kolom `nama` (model `User` juga memuat `foto` dan `role`).
>
> Jalankan SQL berikut setelah `php artisan migrate`, atau buat migrasi sendiri dengan struktur yang sama.

```sql
CREATE TABLE admins (
    id_admin INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

ALTER TABLE users
    CHANGE name nama VARCHAR(255) NOT NULL,
    ADD COLUMN foto VARCHAR(255) NULL,
    ADD COLUMN role VARCHAR(50) NULL;
```

Login memakai tabel `users`, jadi buat satu akun lewat Tinker:

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'nama' => 'Admin',
    'email' => 'admin@example.com',
    'password' => 'rahasia123',   // otomatis di-hash
]);
```

Setelah itu buka `http://localhost:8000` dan login dengan akun tersebut.

### Alur Aplikasi

1. Pengguna membuka `/login` dan mengisi email serta password.
2. `AuthController@cek` memvalidasi input, mencocokkan password dengan `Hash::check`, lalu menyimpan `email`, `nama`, dan `logged_in` ke session.
3. Middleware `IsLogin` memeriksa session `logged_in`; jika tidak ada, pengguna diarahkan ke `/login`.
4. Di menu **Admin**, pengguna dapat menambah, mengubah, dan menghapus data admin. Saat edit, password boleh dikosongkan jika tidak ingin diganti.
5. Tombol **Logout** menghapus session dan kembali ke halaman login.

### Validasi Form

- **Login:** `email` wajib dan berformat email, `password` wajib.
- **Tambah admin:** `username` wajib (maks. 100 karakter), `password` wajib (6 sampai 100 karakter).
- **Edit admin:** `username` wajib (maks. 100 karakter), `password` opsional (6 sampai 100 karakter).

---

## Pengujian

Dari dalam folder proyek yang diinginkan:

```bash
php artisan test
```

## Lisensi

Repositori ini dibuat untuk keperluan pembelajaran. Framework Laravel berlisensi [MIT](https://opensource.org/licenses/MIT).
