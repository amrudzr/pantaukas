<?php

/**
 * File: database/migrations/20240614_create_admin_table.php
 * Deskripsi: Migrasi untuk membuat tabel 'admin'.
 * Tabel ini akan menyimpan informasi pengguna untuk autentikasi.
 */

/**
 * Jalankan migrasi ini untuk membuat tabel 'admin'.
 * @param mysqli $conn Objek koneksi database.
 */
function migrate_create_admin_table(mysqli $conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS admin (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL, -- Tambahan nama lengkap admin
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL, -- Akan menyimpan password yang di-hash
                role ENUM('superadmin', 'admin', 'operator') DEFAULT 'operator', -- Level akses
                status ENUM('active', 'inactive', 'suspended') DEFAULT 'active', -- Status akun
                last_login TIMESTAMP NULL, -- Waktu login terakhir
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL, -- Untuk soft delete
                INDEX (email), -- Index untuk pencarian email
                INDEX (status), -- Index untuk filter status
                INDEX (role) -- Index untuk filter role
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Migrasi 'create_admin_table' berhasil dijalankan.<br>";
    } else {
        echo "Error menjalankan migrasi 'create_admin_table': " . $conn->error . "<br>";
    }
}
