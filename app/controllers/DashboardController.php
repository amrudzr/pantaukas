<?php
require_once APP_PATH . 'models/PaymentCashModel.php';
require_once APP_PATH . 'models/MemberModel.php';
require_once APP_PATH . 'models/TypeFeeModel.php';
require_once APP_PATH . 'models/PaymentFeeModel.php';

class DashboardController
{
    private $paymentModel;
    private $memberModel;
    private $typeFeeModel;
    private $paymentFeeModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentCashModel();
        $this->memberModel = new MemberModel();
        $this->typeFeeModel = new TypeFeeModel();
        $this->paymentFeeModel = new PaymentFeeModel();
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

        // Hitung total iuran dari payment_fee
        $totalIuran = $this->paymentFeeModel->getTotalPaidFees($selectedMonth, $selectedYear);

        // Hitung persentase perubahan kas dan iuran
        $kasChangePct = $this->calculatePercentageChange('kas', $selectedMonth, $selectedYear);
        $iuranChangePct = $this->calculatePercentageChange('iuran', $selectedMonth, $selectedYear);

        // Data anggota
        $allMembers = $this->memberModel->all();
        $anggotaActive = array_filter($allMembers, fn($m) => $m['status'] === 'active');
        $anggotaPassive = array_filter($allMembers, fn($m) => $m['status'] === 'passive');
        $anggotaKeluar = array_filter($allMembers, fn($m) => $m['status'] === 'inactive');

        // Riwayat transaksi terakhir
        $riwayatTransaksi = $this->paymentModel->getRecentTransactions(5);

        // Dapatkan data tunggakan per jenis iuran
        $tunggakanPerJenis = $this->getTunggakanPerJenis($selectedMonth, $selectedYear);

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
            'tunggakanPerJenis' => $tunggakanPerJenis,
            'pageTitle' => 'Dashboard',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/dashboard']
            ]
        ];

        view('dashboard', $data);
    }

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
            $currentValue = $this->paymentFeeModel->getTotalPaidFees($month, $year);
            $previousValue = $this->paymentFeeModel->getTotalPaidFees($prevMonth, $prevYear);
        }

        if ($previousValue == 0) {
            return 0.0;
        }

        return ($currentValue - $previousValue) / $previousValue;
    }

    /**
     * Dapatkan data tunggakan per jenis iuran
     */
    private function getTunggakanPerJenis($month, $year): array
    {
        $userId = $_SESSION['user_id'];
        $jenisIuran = $this->typeFeeModel->all();
        $result = [];

        foreach ($jenisIuran as $jenis) {
            // Hitung jumlah anggota yang belum bayar untuk jenis iuran ini
            $unpaidCount = $this->paymentFeeModel->countUnpaidMembers($jenis['id'], $month, $year);

            if ($unpaidCount > 0) {
                $result[] = [
                    'jenis_iuran' => $jenis['name'],
                    'jumlah_tunggakan' => $unpaidCount,
                    'target_nominal' => $jenis['nominal'],
                    'total_potensi' => $unpaidCount * $jenis['nominal']
                ];
            }
        }

        return $result;
    }
}
