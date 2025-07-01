<?php

/**
 * File: database/seeders/AdminSeeder.php
 * Deskripsi: Seeder untuk mengisi data dummy ke tabel 'admin'.
 */

/**
 * Jalankan seeder ini untuk mengisi data dummy ke tabel 'admin'.
 * @param mysqli $conn Objek koneksi database.
 */
function seed_admin_table(mysqli $conn)
{
    // Password dummy akan di-hash sebelum disimpan
    $superadminPassword = password_hash('superadmin123', PASSWORD_DEFAULT); // Password untuk superadmin
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT); // Password untuk admin

    $admin_data = [
        ['Super Admin', 'superadmin@example.com', $superadminPassword, 'superadmin', 'active'],
        ['Admin', 'admin@example.com', $adminPassword, 'admin', 'active'],
    ];

    $stmt = $conn->prepare("INSERT INTO admin (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement for AdminSeeder: " . $conn->error . "<br>";
        return;
    }

    foreach ($admin_data as $admin) {
        $name = $admin[0];
        $email = $admin[1];
        $password = $admin[2];
        $role = $admin[3];
        $status = $admin[4];

        if ($stmt->bind_param("sssss", $name, $email, $password, $role, $status) && $stmt->execute()) {
            echo "Admin '{$name}' berhasil ditambahkan.<br>";
        } else {
            if ($conn->errno == 1062) { // Duplicate entry error
                echo "Peringatan: Admin '{$name}' dengan email '{$email}' sudah ada (duplikat).<br>";
            } else {
                echo "Error menambahkan admin '{$name}': " . $stmt->error . "<br>";
            }
        }
    }
    $stmt->close();
}
