<?php

/**
 * File: config/database.php
 * Deskripsi: Berisi pengaturan koneksi ke database MySQL.
 */

define('DB_HOST', 'localhost'); // Host database (biasanya localhost)
define('DB_USER', 'root');     // Username database Anda
define('DB_PASS', '');         // Password database Anda (kosong jika tidak ada)
define('DB_NAME', 'pantaukas'); // Nama database yang telah Anda buat

// Fungsi untuk membuat koneksi database
function getDbConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }
    return $conn;
}
