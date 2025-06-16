<?php

/**
 * File: database/setup.php
 * Deskripsi: Skrip ini bertanggung jawab untuk menjalankan migrasi dan seeder.
 * Dijalankan melalui rute khusus (misalnya /db/init).
 */

// Pastikan PATH sudah didefinisikan (dari index.php)
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', __DIR__ . '/../config/');
}

// Sertakan file konfigurasi database untuk mendapatkan koneksi
require_once CONFIG_PATH . 'database.php';

// Mendapatkan koneksi database
$conn = getDbConnection();

// --- Fungsi untuk menjalankan Migrasi ---
function runMigrations(mysqli $conn)
{
    echo "<h2>Menjalankan Migrasi...</h2>";
    $migrationsPath = __DIR__ . '/migrations/';
    $migrationFiles = scandir($migrationsPath);

    sort($migrationFiles); // Urutkan file berdasarkan nama untuk memastikan dependensi
    foreach ($migrationFiles as $file) {
        // Lewati entri direktori '.' dan '..'
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            require_once $migrationsPath . $file;
            // Dapatkan nama fungsi migrasi dari nama file
            // Contoh: 20250613_134320_create_admin_table.php -> migrate_create_admin_table
            // Menggunakan substr($file, 16) untuk menghapus 'YYYYMMDD_HHMMSS_'
            $functionName = 'migrate_' . str_replace('.php', '', substr($file, 16));

            if (function_exists($functionName)) {
                echo "Menjalankan migrasi: " . $file . "<br>";
                $functionName($conn);
            } else {
                echo "Peringatan: Fungsi '$functionName' tidak ditemukan dalam file '$file'.<br>";
            }
        }
    }
    echo "Migrasi selesai.<br>";
}

// --- Fungsi untuk menjalankan Seeder ---
function runSeeders(mysqli $conn)
{
    echo "<h2>Menjalankan Seeder...</h2>";
    // Bersihkan tabel sebelum seeding untuk menghindari duplikasi data dummy setiap kali dijalankan
    // HATI-HATI: Ini akan menghapus semua data di tabel!
    echo "Membersihkan tabel...<br>";

    // Penting: Urutan TRUNCATE harus dari tabel anak (yang memiliki Foreign Key) ke tabel induk.
    // Member memiliki FK ke user, jadi member harus di-truncate duluan.
    // Admin dan Books tidak memiliki FK ke user/member dalam skema yang Anda berikan.
    // Jika ada siklus dependensi (misal A -> B dan B -> A), ini bisa jadi masalah,
    // tapi untuk proyek sederhana ini, urutan ini aman.
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;"); // Nonaktifkan FK checks sementara

    // Urutan TRUNCATE: member (anak dari user), books, user, admin
    // Sesuaikan urutan ini jika ada Foreign Key baru atau perubahan hubungan tabel
    // $conn->query("TRUNCATE TABLE IF EXISTS member"); // Tambahkan IF EXISTS untuk mencegah error jika tabel belum ada
    // $conn->query("TRUNCATE TABLE IF EXISTS user");
    // $conn->query("TRUNCATE TABLE IF EXISTS admin");
    // echo "Tabel telah dibersihkan.<br>";

    $conn->query("SET FOREIGN_KEY_CHECKS = 1;"); // Aktifkan kembali FK checks

    $seedersPath = __DIR__ . '/seeders/';
    $seederFiles = scandir($seedersPath);

    sort($seederFiles); // Urutkan file berdasarkan nama
    foreach ($seederFiles as $file) {
        // Lewati entri direktori '.' dan '..'
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            require_once $seedersPath . $file;
            // Dapatkan nama fungsi seeder dari nama file
            // Contoh: AdminSeeder.php -> seed_admin_table
            // Contoh: UserSeeder.php -> seed_user_table
            // Asumsi seeder mengikuti nama file tanpa timestamp (misal: 'UserSeeder.php')
            $functionName = 'seed_' . strtolower(str_replace('Seeder.php', '_table', $file));

            if (function_exists($functionName)) {
                echo "Menjalankan seeder: " . $file . "<br>";
                $functionName($conn);
            } else {
                echo "Peringatan: Fungsi '$functionName' tidak ditemukan dalam file '$file'.<br>";
            }
        }
    }
    echo "Seeding selesai.<br>";
}

// Jalankan migrasi dan seeder
runMigrations($conn);
runSeeders($conn);

// Tutup koneksi database
$conn->close();

echo "<h1>Setup Database Selesai!</h1>";
echo "<p>Anda sekarang bisa kembali ke <a href=\"/\">halaman utama</a>, <a href=\"/login\">login</a>, atau <a href=\"/register\">registrasi</a>.</p>";
