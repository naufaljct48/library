# Library Management System

Library Management System adalah aplikasi berbasis web yang dibangun menggunakan Laravel untuk mengelola peminjaman dan pengembalian buku.

## 🛠️ Teknologi yang Digunakan

-   **Laravel 11** (PHP Framework)
-   **MySQL** (Database)
-   **Bootstrap 5 - Tabler** (Frontend Styling)
-   **Carbon** (Manipulasi Tanggal)

## 🚀 Instalasi

1. **Clone Repository**
    ```sh
    git clone https://github.com/naufaljct48/library.git
    cd library
    ```
2. **Install Dependencies**
    ```sh
    composer install
    ```
3. **Konfigurasi Environment**

    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

    Sesuaikan konfigurasi database di `.env`.

4. **Migrasi Database**

    ```sh
    php artisan migrate --seed
    ```

5. **Jalankan Server**
    ```sh
    php artisan serve
    ```
    Akses aplikasi di `http://127.0.0.1:8000`

## 🔑 Akun Default

**Admin**

-   Email: `admin@admin.com`
-   Password: `password`

**Member**

-   Email: `test@example.com`
-   Password: `password`

## 📌 Fitur Utama

-   🏠 **Autentikasi** (Login & Register)
-   📚 **Manajemen Buku** (Tambah, Edit, Hapus)
-   🔄 **Peminjaman & Pengembalian Buku**
-   🛑 **Middleware Admin & User**
-   📊 **Dashboard dengan Statistik Peminjaman**

## 📸 Showcase  
**Admin**  
<img src="https://github.com/user-attachments/assets/12335e52-5489-4ee9-9391-65ac8a2fbb3d" width="400">  

**Member**  
<img src="https://github.com/user-attachments/assets/a3afa71c-1cdd-40cd-8de6-8e95e02cf171" width="400">  

## 🛠️ Troubleshooting

Jika terjadi error setelah migrasi, jalankan:

```sh
php artisan migrate:refresh --seed
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear
composer dump-autoload
```

## 📜 Lisensi

Proyek ini dirilis di bawah lisensi MIT. Bebas digunakan dan dikembangkan! 🎉
