<?php

/**
 * File: database/seeders/UserSeeder.php
 * Deskripsi: Seeder untuk mengisi data dummy ke tabel 'user'.
 * Diperbarui untuk memasukkan user dengan status 'active', 'blocked', dan 'deleted' 
 * serta mencakup kolom id_admin untuk user yang diblokir/dihapus.
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
    $userDeletedPassword = password_hash('userdeleted123', PASSWORD_DEFAULT);

    // Data admin dummy (asumsi sudah ada di tabel admin)
    // ID admin 1 dan 2 diasumsikan sudah ada
    $adminIds = [1, 2];

    $users_data = [
        // User aktif (tanpa id_admin)
        ['Alice', '081234567890', $userActivePassword, 'active', null],
        
        // User diblokir oleh admin 1
        ['Bob', '089876543210', $userBlockedPassword, 'blocked', $adminIds[0]],
        
        // User dihapus oleh admin 2
        ['Charlie', '087654321098', $userDeletedPassword, 'deleted', $adminIds[1]],
        
        // User aktif lainnya
        ['David', '085678901234', $userActivePassword, 'active', null],
        
        // User diblokir lainnya
        ['Eve', '082345678901', $userBlockedPassword, 'blocked', $adminIds[1]]
    ];

    $stmt = $conn->prepare("INSERT INTO user (name, phone, password, status, id_admin) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement for UserSeeder: " . $conn->error . "<br>";
        return;
    }

    foreach ($users_data as $user) {
        $name = $user[0];
        $phone = $user[1];
        $password = $user[2];
        $status = $user[3];
        $id_admin = $user[4];

        if ($stmt->bind_param("ssssi", $name, $phone, $password, $status, $id_admin) && $stmt->execute()) {
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