<?php
require_once APP_PATH . 'models/PaymentCashModel.php';
require_once APP_PATH . 'models/MemberModel.php';

class DashboardController
{
    private $paymentModel;
    private $memberModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentCashModel();
        $this->memberModel = new MemberModel();
    }

    /** GET /dashboard */
    public function index()
    {
        // Ambil bulan dan tahun dari query string
        $selectedMonth = $_GET['bulan'] ?? date('m');
        $selectedYear = $_GET['tahun'] ?? date('Y');

        // Hitung total kas
        $totalPemasukan = $this->paymentModel->sumByTypeAndDate('in', $selectedMonth, $selectedYear);
        $totalPengeluaran = $this->paymentModel->sumByTypeAndDate('out', $selectedMonth, $selectedYear);
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        // Hitung total iuran (asumsi iuran adalah transaksi dengan kategori tertentu)
        $totalIuran = $this->paymentModel->sumByCategoryAndDate('iuran', $selectedMonth, $selectedYear);

        // Hitung persentase perubahan kas dan iuran (dibandingkan dengan bulan sebelumnya)
        $kasChangePct = $this->calculatePercentageChange('kas', $selectedMonth, $selectedYear);
        $iuranChangePct = $this->calculatePercentageChange('iuran', $selectedMonth, $selectedYear);

        // Data anggota
        $allMembers = $this->memberModel->all();
        $anggotaActive = array_filter($allMembers, fn($m) => $m['status'] === 'active');
        $anggotaPassive = array_filter($allMembers, fn($m) => $m['status'] === 'passive');
        $anggotaKeluar = array_filter($allMembers, fn($m) => $m['status'] === 'inactive');

        // Riwayat transaksi terakhir
        $riwayatTransaksi = $this->paymentModel->getRecentTransactions(5);

        // Anggota dengan tunggakan (contoh implementasi sederhana)
        $anggotaTunggakan = $this->getAnggotaTunggakan();

        // Siapkan data untuk view
        $data = [
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'totalKas' => $saldoKas,
            'kasChangePct' => $kasChangePct,
            'totalIuran' => $totalIuran,
            'iuranChangePct' => $iuranChangePct,
            'jumlahAnggota' => count($allMembers),
            'anggotaActive' => count($anggotaActive),
            'anggotaPassive' => count($anggotaPassive),
            'anggotaKeluar' => count($anggotaKeluar),
            'riwayatTransaksi' => $riwayatTransaksi,
            'anggotaTunggakan' => $anggotaTunggakan,
            'pageTitle' => 'Dashboard',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/dashboard']
            ]
        ];

        view('dashboard', $data);
    }

    /**
     * Hitung persentase perubahan dari bulan sebelumnya
     */
    private function calculatePercentageChange(string $type, string $month, string $year): float
    {
        $prevMonth = $month - 1;
        $prevYear = $year;

        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $currentValue = 0;
        $previousValue = 0;

        if ($type === 'kas') {
            $currentPemasukan = $this->paymentModel->sumByTypeAndDate('in', $month, $year);
            $currentPengeluaran = $this->paymentModel->sumByTypeAndDate('out', $month, $year);
            $currentValue = $currentPemasukan - $currentPengeluaran;

            $prevPemasukan = $this->paymentModel->sumByTypeAndDate('in', $prevMonth, $prevYear);
            $prevPengeluaran = $this->paymentModel->sumByTypeAndDate('out', $prevMonth, $prevYear);
            $previousValue = $prevPemasukan - $prevPengeluaran;
        } elseif ($type === 'iuran') {
            $currentValue = $this->paymentModel->sumByCategoryAndDate('iuran', $month, $year);
            $previousValue = $this->paymentModel->sumByCategoryAndDate('iuran', $prevMonth, $prevYear);
        }

        if ($previousValue == 0) {
            return 0.0;
        }

        return ($currentValue - $previousValue) / $previousValue;
    }

    /**
     * Dapatkan daftar anggota dengan tunggakan
     * (Ini adalah contoh sederhana, implementasi sebenarnya mungkin lebih kompleks)
     */
    private function getAnggotaTunggakan(): array
    {
        // Asumsi ada model Iuran yang menangani data iuran anggota
        // Ini hanya contoh sederhana
        return [
            ['nama' => 'Ahmad', 'status' => 'Hutang', 'jumlah' => 50000],
            ['nama' => 'Budi', 'status' => 'Belum Bayar', 'jumlah' => 50000]
        ];
    }
}
