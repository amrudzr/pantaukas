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
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            throw new Exception("Koneksi database gagal: " . $conn->connect_error);
        }

        return $conn;
    } catch (Exception $e) {
        // Variabel yang dikirim ke layout view
        $errorMessage = $e->getMessage(); // pesan utama
        $errorDetails = $e->getTraceAsString(); // detail tumpukan error
        $pageTitle = "Koneksi Gagal";
        $breadcrumbs = [];
        $contentView = 'errors/500.php';
        include APP_PATH . 'views/layout/app.php';
        exit;
    }
}
