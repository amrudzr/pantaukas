<?php

/**
 * File: database/seeders/UserSeeder.php
 * Deskripsi: Seeder untuk mengisi data dummy ke tabel 'user'.
 * Diperbarui untuk memasukkan user dengan status 'active' dan 'blocked'.
 */

/**
 * Jalankan seeder ini untuk mengisi data dummy ke tabel 'user'.
 * @param mysqli $conn Objek koneksi database.
 */
function seed_user_table(mysqli $conn)
{
    // Password dummy akan di-hash sebelum disimpan
    $userActivePassword = password_hash('useractive123', PASSWORD_DEFAULT);
    $userBlockedPassword = password_hash('userblocked123', PASSWORD_DEFAULT);

    $users_data = [
        ['Alice', '081234567890', $userActivePassword, 'active'],
        ['Bob', '089876543210', $userBlockedPassword, 'blocked'],
    ];

    $stmt = $conn->prepare("INSERT INTO user (name, phone, password, status) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement for UserSeeder: " . $conn->error . "<br>";
        return;
    }

    foreach ($users_data as $user) {
        $name = $user[0];
        $phone = $user[1];
        $password = $user[2];
        $status = $user[3];

        if ($stmt->bind_param("ssss", $name, $phone, $password, $status) && $stmt->execute()) {
            echo "Pengguna '{$name}' berhasil ditambahkan.<br>";
        } else {
            if ($conn->errno == 1062) { // Duplicate entry for UNIQUE key 'phone'
                echo "Peringatan: Pengguna '{$name}' dengan nomor telepon '{$phone}' sudah ada (duplikat).<br>";
            } else {
                echo "Error menambahkan pengguna '{$name}': " . $stmt->error . "<br>";
            }
        }
    }
    $stmt->close();
}
