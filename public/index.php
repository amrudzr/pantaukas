<?php
/**
 * File: public/index.php
 * Deskripsi: Front Controller utama aplikasi. Semua permintaan masuk melalui file ini.
 * Fungsi utamanya adalah:
 * 1. Mendefinisikan path ke folder-folder penting dalam aplikasi.
 * 2. Memuat file konfigurasi database.
 * 3. Memuat model dan controller yang diperlukan (meskipun sebagian besar controller
 * dan model akan dimuat secara dinamis oleh sistem routing).
 * 4. Memuat sistem routing untuk menentukan controller dan method mana yang harus dieksekusi
 * berdasarkan URL yang diminta.
 * 5. Menganalisis URI (Uniform Resource Identifier) yang diminta oleh pengguna.
 * 6. Meneruskan URI ke fungsi `dispatchRequest` untuk diproses lebih lanjut oleh sistem routing.
 * 7. Telah ditambahkan `session_start()` untuk mengaktifkan manajemen sesi pengguna.
 */

// Definisikan path ke folder-folder penting di aplikasi.
// MULAI SESI PHP. Ini harus dipanggil di awal setiap skrip yang menggunakan $_SESSION.
// Penting: Pastikan tidak ada output ke browser sebelum `session_start()`.
session_start();

// APP_PATH menunjuk ke direktori 'app' yang berisi logika aplikasi (controllers, models, views).
define('APP_PATH', __DIR__ . '/../app/');
// CONFIG_PATH menunjuk ke direktori 'config' yang berisi konfigurasi aplikasi (misal: database).
define('CONFIG_PATH', __DIR__ . '/../config/');
// DATABASE_PATH menunjuk ke direktori 'database' yang berisi migrasi dan seeder.
define('DATABASE_PATH', __DIR__ . '/../database/');
// HELPER_PATH menunjuk ke direktori 'app/helpers' yang berisi fungsi-fungsi pembantu (helper).
// Fungsi-fungsi ini bersifat global dan dapat digunakan di berbagai bagian aplikasi (controller, view, dsb).
// Tujuan utamanya adalah agar fungsi yang sering digunakan (seperti format rupiah, redirect, validasi sederhana)
// bisa didefinisikan di satu tempat dan digunakan ulang tanpa pengulangan kode.
define('HELPER_PATH', __DIR__ . '/../app/helpers/');


// Sertakan file konfigurasi database.
// File ini berisi informasi koneksi ke database dan fungsi helper untuk mendapatkan koneksi.
require_once CONFIG_PATH . 'database.php';

// Sertakan file sistem routing.
// File ini akan berisi logika untuk memetakan URL ke controller dan method yang sesuai.
// Di dalamnya juga akan otomatis memuat controller yang dibutuhkan oleh rute.
require_once APP_PATH . 'routes.php';

// Sertakan file helper.
// File ini berisi kumpulan fungsi-fungsi global yang dapat digunakan di seluruh aplikasi.
// Disertakan di sini agar fungsi helper tersedia sejak awal dan tidak perlu require ulang di setiap file.
require_once HELPER_PATH . 'helpers.php';

// Mendapatkan URI yang diminta oleh pengguna dari URL.
// parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) akan mengambil hanya path dari URL (misal: /books/3/detail).
// trim(..., '/') akan menghapus slash di awal dan akhir string, menjadikannya 'books/3/detail'.
$requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Mengarahkan permintaan yang masuk.
// Fungsi `dispatchRequest` (didefinisikan di routes.php) akan menganalisis $requestUri
// dan memanggil method yang sesuai di controller yang relevan.
dispatchRequest($requestUri);
