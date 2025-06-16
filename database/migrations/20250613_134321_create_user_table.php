<?php

/**
 * File: database/migrations/20240614_create_user_table.php
 * Deskripsi: Migrasi untuk membuat tabel 'user'.
 * Tabel ini akan menyimpan informasi pengguna untuk autentikasi.
 */

/**
 * Jalankan migrasi ini untuk membuat tabel 'user'.
 * @param mysqli $conn Objek koneksi database.
 */
function migrate_create_user_table(mysqli $conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS user (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                phone VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL, -- Akan menyimpan password yang di-hash
                status ENUM('active', 'blocked', 'deleted') DEFAULT 'active',
                last_login TIMESTAMP NULL, -- Waktu login terakhir
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Auto-update
                deleted_at TIMESTAMP NULL -- Sebaiknya NULL default untuk soft delete
                -- Index sudah ada di definisi kolom, tidak perlu ditulis ulang di sini
                -- INDEX (status) -- Index untuk mempercepat query berdasarkan status
                -- INDEX (phone) -- Index sudah ada karena UNIQUE, tapi ditulis eksplisit
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Migrasi 'create_user_table' berhasil dijalankan.<br>";
    } else {
        echo "Error menjalankan migrasi 'create_user_table': " . $conn->error . "<br>";
    }
}
