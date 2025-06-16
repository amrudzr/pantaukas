<?php

/**
 * File: database/migrations/20240614_create_type_fee_table.php
 * Deskripsi: Migrasi untuk membuat tabel 'type_fee'.
 * Tabel ini akan menyimpan informasi pengguna untuk autentikasi.
 */

/**
 * Jalankan migrasi ini untuk membuat tabel 'type_fee'.
 * @param mysqli $conn Objek koneksi database.
 */
function migrate_create_type_fee_table(mysqli $conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS type_fee (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                nominal INT NOT NULL,
                duration ENUM('daily', 'weekly', 'monthly', 'annually') NOT NULL,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE RESTRICT
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Migrasi 'create_type_fee_table' berhasil dijalankan.<br>";
    } else {
        echo "Error menjalankan migrasi 'create_type_fee_table': " . $conn->error . "<br>";
    }
}
