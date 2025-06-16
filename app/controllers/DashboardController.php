<?php

/**
 * File: app/controllers/DashboardController.php
 * Deskripsi: Controller untuk halaman dashboard.
 */

class DashboardController
{
    function showDashboard()
    {
        // Ambil bulan dan tahun dari query string
        $selectedMonth = $_GET['bulan'] ?? date('m');
        $selectedYear  = $_GET['tahun'] ?? date('Y');

        // Dummy data (siapkan untuk diambil dari database nanti)
        $data = [
            'selectedMonth'     => $selectedMonth,
            'selectedYear'      => $selectedYear,

            'totalKas'          => 2500000,
            'kasChangePct'      => 0.15,
            'totalIuran'        => 1350000,
            'iuranChangePct'    => 0.10,

            'jumlahAnggota'     => 25,
            'anggotaActive'     => 20,
            'anggotaPassive'    => 3,
            'anggotaKeluar'     => 2,

            'riwayatTransaksi'  => [
                ['tanggal' => '2025-06-10', 'keterangan' => 'Pembayaran iuran', 'kategori' => 'Iuran', 'jumlah' => 50000],
                ['tanggal' => '2025-06-09', 'keterangan' => 'Pembelian ATK', 'kategori' => 'Pengeluaran', 'jumlah' => 30000],
                ['tanggal' => '2025-06-08', 'keterangan' => 'Iuran bulanan', 'kategori' => 'Iuran', 'jumlah' => 50000],
            ],

            'anggotaTunggakan'  => [
                ['nama' => 'Ahmad', 'status' => 'Hutang', 'jumlah' => 50000],
                ['nama' => 'Budi', 'status' => 'Belum Bayar', 'jumlah' => 50000],
            ],

            // Optional tambahan informasi untuk layout
            'pageTitle'         => 'Dashboard',
            'breadcrumbs'       => [
                ['label' => 'Dashboard', 'url' => '/dashboard'],
            ]
        ];

        // Tampilkan view dashboard
        view('dashboard', $data);
    }
}
