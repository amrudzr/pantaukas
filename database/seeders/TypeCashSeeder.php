<?php

/**
 * File: database/seeders/TypeCashSeeder.php
 * Deskripsi: Seeder untuk mengisi kategori transaksi kas umum (global).
 */

function seed_typecash_table(mysqli $conn)
{
    $type_cash_data = [
        ['Gaji', 'Pendapatan dari pekerjaan'],
        ['Bonus', 'Bonus tambahan'],
        ['Iuran Bulanan', 'Iuran rutin per bulan'],
        ['Donasi Masuk', 'Sumbangan atau donasi'],
        ['Penjualan Barang', 'Pendapatan dari penjualan'],
        ['Hadiah', 'Uang hadiah atau reward'],
        ['Pembayaran Utang', 'Uang masuk dari pembayaran utang'],
        ['Uang Muka', 'Pendapatan uang muka transaksi'],
        ['Pengembalian Dana', 'Refund dari transaksi sebelumnya'],
        ['Belanja Kebutuhan', 'Pengeluaran harian/bulanan'],
        ['Transportasi', 'Ongkos perjalanan'],
        ['Makan & Minum', 'Pembelian konsumsi'],
        ['Pembayaran Tagihan', 'Listrik, air, internet, dll.'],
        ['Sewa Tempat', 'Biaya sewa rumah/tempat'],
        ['Pembelian Aset', 'Pembelian barang modal'],
        ['Biaya Operasional', 'Pengeluaran operasional umum'],
        ['Gaji Pegawai', 'Pembayaran gaji staf'],
        ['Dana Sosial', 'Sumbangan sosial'],
        ['Lain-lain', 'Transaksi tidak terklasifikasi'],
    ];

    $stmt = $conn->prepare("INSERT INTO type_cash (id_user, name, description) VALUES (NULL, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement for TypeCashSeeder: " . $conn->error . "<br>";
        return;
    }

    foreach ($type_cash_data as [$name, $desc]) {
        $stmt->bind_param("ss", $name, $desc);
        if ($stmt->execute()) {
            echo "Kategori '{$name}' berhasil ditambahkan.<br>";
        } else {
            echo "Gagal menambahkan kategori '{$name}': " . $stmt->error . "<br>";
        }
    }

    $stmt->close();
}