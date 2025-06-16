<?php

/**
 * File: database/migrations/20240614_create_payment_cash_table.php
 * Deskripsi: Migrasi untuk membuat tabel 'payment_cash'.
 * Tabel ini akan menyimpan informasi pengguna untuk autentikasi.
 */

/**
 * Jalankan migrasi ini untuk membuat tabel 'payment_cash'.
 * @param mysqli $conn Objek koneksi database.
 */
function migrate_create_payment_cash_table(mysqli $conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS payment_cash (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_type_cash INT NULL,
                id_user INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                payment_date DATETIME NOT NULL,
                nominal INT NOT NULL,
                type ENUM('in', 'out') NOT NULL,
                attachment TEXT NULL,
                notes VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                FOREIGN KEY (id_type_cash) REFERENCES type_cash(id) ON DELETE SET NULL,
                FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE RESTRICT
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Migrasi 'create_payment_cash_table' berhasil dijalankan.<br>";
    } else {
        echo "Error menjalankan migrasi 'create_payment_cash_table': " . $conn->error . "<br>";
    }
}
