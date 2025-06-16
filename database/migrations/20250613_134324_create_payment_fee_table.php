<?php

/**
 * File: database/migrations/20240614_create_payment_fee_table.php
 * Deskripsi: Migrasi untuk membuat tabel 'payment_fee'.
 * Tabel ini akan menyimpan informasi pengguna untuk autentikasi.
 */

/**
 * Jalankan migrasi ini untuk membuat tabel 'payment_fee'.
 * @param mysqli $conn Objek koneksi database.
 */
function migrate_create_payment_fee_table(mysqli $conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS payment_fee (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_type_fee INT NOT NULL,
                id_member INT NOT NULL,
                id_user INT NOT NULL,
                payment_date DATETIME NOT NULL,
                nominal INT NOT NULL,
                status ENUM('paid', 'debt', 'unpaid') NOT NULL,
                notes VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                FOREIGN KEY (id_type_fee) REFERENCES type_fee(id) ON DELETE RESTRICT,
                FOREIGN KEY (id_member) REFERENCES member(id) ON DELETE RESTRICT,
                FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE RESTRICT
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Migrasi 'create_payment_fee_table' berhasil dijalankan.<br>";
    } else {
        echo "Error menjalankan migrasi 'create_payment_fee_table': " . $conn->error . "<br>";
    }
}
