<?php

/**
 * File: database/migrations/20240614_create_member_table.php
 * Deskripsi: Migrasi untuk membuat tabel 'member'.
 * Tabel ini akan menyimpan informasi pengguna untuk autentikasi.
 */

/**
 * Jalankan migrasi ini untuk membuat tabel 'member'.
 * @param mysqli $conn Objek koneksi database.
 */
function migrate_create_member_table(mysqli $conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS member (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NULL,
                address TEXT NULL,
                status ENUM('active', 'passive', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL, -- Untuk soft delete
                FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE RESTRICT,
                INDEX (id_user), -- Index untuk relasi
                INDEX (status) -- Index untuk filter status
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Migrasi 'create_member_table' berhasil dijalankan.<br>";
    } else {
        echo "Error menjalankan migrasi 'create_member_table': " . $conn->error . "<br>";
    }
}
