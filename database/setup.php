<?php

// Pastikan konstanta ini didefinisikan
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', __DIR__ . '/../config/');
if (!defined('APP_PATH')) define('APP_PATH', realpath(__DIR__ . '/../app/') . '/');

// Hanya definisikan konstanta jika belum didefinisikan
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('');
if (!defined('DB_NAME')) define('pantaukas');

// Buat database jika belum ada
function createDatabaseIfNotExists()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $check = $conn->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($check->num_rows === 0) {
        if ($conn->query("CREATE DATABASE " . DB_NAME)) {
            echo "Database '" . DB_NAME . "' berhasil dibuat.<br>";
        } else {
            die("Gagal membuat database: " . $conn->error);
        }
    } else {
        echo "Database '" . DB_NAME . "' sudah ada.<br>";
    }

    $conn->close();
}

// Jalankan langkah awal
createDatabaseIfNotExists();

// Setelah database dipastikan ada, include koneksi
require_once CONFIG_PATH . 'database.php'; // hanya sekarang kita include

$conn = getDbConnection(); // ← gunakan dari file database.php

// MIGRASI
function runMigrations(mysqli $conn)
{
    echo "<h2>Menjalankan Migrasi...</h2>";
    $files = scandir(__DIR__ . '/migrations/');
    sort($files);
    foreach ($files as $file) {
        if (str_ends_with($file, '.php')) {
            require_once __DIR__ . "/migrations/$file";
            $func = 'migrate_' . str_replace('.php', '', substr($file, 16));
            if (function_exists($func)) {
                echo "Menjalankan migrasi: $file<br>";
                $func($conn);
            } else {
                echo "Fungsi migrasi '$func' tidak ditemukan di $file<br>";
            }
        }
    }
    echo "Migrasi selesai.<br>";
}

// SEEDER
function runSeeders(mysqli $conn)
{
    echo "<h2>Menjalankan Seeder...</h2>";
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;");

    $files = scandir(__DIR__ . '/seeders/');
    sort($files);
    foreach ($files as $file) {
        if (str_ends_with($file, '.php')) {
            require_once __DIR__ . "/seeders/$file";
            $func = 'seed_' . strtolower(str_replace('Seeder.php', '_table', $file));
            if (function_exists($func)) {
                echo "Menjalankan seeder: $file<br>";
                try {
                    $func($conn);
                } catch (mysqli_sql_exception $e) {
                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                        echo "Peringatan: Data duplikat di $file. Lewati.<br>";
                    } else {
                        echo "Error saat seeding '$file': " . $e->getMessage() . "<br>";
                    }
                }
            } else {
                echo "Fungsi seeder '$func' tidak ditemukan.<br>";
            }
        }
    }

    $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
    echo "Seeding selesai.<br>";
}

// Eksekusi
runMigrations($conn);
runSeeders($conn);
$conn->close();

echo "<h1>Setup Database Selesai!</h1>";
echo "<p><a href='/'>Kembali ke beranda</a> | <a href='/login'>Login</a> | <a href='/register'>Registrasi</a></p>";
