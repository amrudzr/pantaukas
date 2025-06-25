<?php
require_once APP_PATH . 'models/PaymentCashModel.php';
require_once APP_PATH . 'models/TypeCashModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController
{
    private $paymentCashModel;

    public function __construct()
    {
        $this->paymentCashModel = new PaymentCashModel();
    }

    /** GET /reports */
    public function index()
    {
        view('report/index', [
            'pageTitle' => 'Laporan',
            'breadcrumbs' => [
                ['label' => 'Laporan', 'url' => '/reports']
            ],
        ]);
    }

    /** GET /reports/view */
    public function view()
    {
        $jenis = $_GET['jenis'] ?? '';
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');

        if ($jenis !== 'kas') {
            $_SESSION['flash'] = ['danger', 'Jenis laporan tidak valid.'];
            redirect('/reports');
        }

        $transactions = $this->getTransactionsByMonth($bulan, $tahun);
        $summary = $this->getSummaryByMonth($bulan, $tahun);

        view('report/view', [
            'transactions' => $transactions,
            'summary' => $summary,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'pageTitle' => 'Laporan' . $this->getMonthName($bulan) . ' ' . $tahun,
            'breadcrumbs' => [
                ['label' => 'Laporan', 'url' => '/reports'],
                ['label' => 'Detail Laporan', 'url' => '']
            ],
        ]);
    }

    /** GET /reports/export */
    public function export()
    {
        try {
            // Ambil parameter dari URL
            $jenis = $_GET['jenis'] ?? '';
            $bulan = $_GET['bulan'] ?? date('m');
            $tahun = $_GET['tahun'] ?? date('Y');

            // Debug: Log parameter yang diterima
            error_log("Export Params - Jenis: $jenis, Bulan: $bulan, Tahun: $tahun");

            if ($jenis !== 'kas') {
                throw new Exception('Jenis laporan tidak valid. Hanya laporan kas yang tersedia.');
            }

            // Validasi input
            if (!is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
                throw new Exception('Bulan harus antara 1-12');
            }

            if (!is_numeric($tahun) || $tahun < 2020 || $tahun > date('Y')) {
                throw new Exception('Tahun harus antara 2020-' . date('Y'));
            }

            // Dapatkan data
            $transactions = $this->getTransactionsByMonth($bulan, $tahun);
            $summary = $this->getSummaryByMonth($bulan, $tahun);

            // Debug: Log jumlah data yang ditemukan
            error_log("Jumlah transaksi ditemukan: " . count($transactions));

            if (empty($transactions)) {
                throw new Exception('Tidak ada data '.$jenis.' untuk periode ini');
            }

            $filename = 'Laporan_'.$jenis.'_' . $this->getMonthName($bulan) . '_' . $tahun;

            // Generate Excel
            $this->exportToExcel($transactions, $summary, $filename, $bulan, $tahun);
        } catch (Exception $e) {
            // Log error lengkap
            error_log("Error in ReportController::export(): " . $e->getMessage());
            error_log("Stack Trace: " . $e->getTraceAsString());

            $_SESSION['flash'] = ['danger', 'Gagal mengekspor laporan: ' . $e->getMessage()];
            redirect('/reports');
        }
    }

    private function getTransactionsByMonth($month, $year)
    {
        $userId = $_SESSION['user_id'];
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));

        $stmt = $this->paymentCashModel->getDb()->prepare("
            SELECT pc.*, tc.name AS category_name 
            FROM payment_cash pc 
            LEFT JOIN type_cash tc ON pc.id_type_cash = tc.id 
            WHERE pc.id_user = ? 
                AND pc.payment_date BETWEEN ? AND ?
                AND pc.deleted_at IS NULL
            ORDER BY pc.payment_date DESC, pc.type DESC
        ");
        $stmt->bind_param("iss", $userId, $startDate, $endDate);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function getSummaryByMonth($month, $year)
    {
        $pemasukan = $this->paymentCashModel->sumByTypeAndDate('in', $month, $year);
        $pengeluaran = $this->paymentCashModel->sumByTypeAndDate('out', $month, $year);
        $saldo = $pemasukan - $pengeluaran;

        return [
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'saldo' => $saldo
        ];
    }

    private function getMonthName($monthNumber)
    {
        $months = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
        return $months[$monthNumber - 1] ?? '';
    }

    private function exportToExcel($transactions, $summary, $filename, $bulan, $tahun)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul laporan
        $sheet->setCellValue('A1', 'LAPORAN KAS');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set periode
        $sheet->setCellValue('A2', 'Periode: ' . $this->getMonthName($bulan) . ' ' . $tahun);
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set ringkasan
        $sheet->setCellValue('A4', 'TOTAL PEMASUKAN');
        $sheet->setCellValue('B4', format_rupiah($summary['pemasukan']));
        $sheet->setCellValue('A5', 'TOTAL PENGELUARAN');
        $sheet->setCellValue('B5', format_rupiah($summary['pengeluaran']));
        $sheet->setCellValue('A6', 'SALDO KAS');
        $sheet->setCellValue('B6', format_rupiah($summary['saldo']));

        // Style untuk ringkasan
        $sheet->getStyle('A4:A6')->getFont()->setBold(true);
        $sheet->getStyle('B4:B6')->getNumberFormat()->setFormatCode('#,##0');

        // Header tabel transaksi
        $sheet->setCellValue('A8', 'TANGGAL');
        $sheet->setCellValue('B8', 'KATEGORI');
        $sheet->setCellValue('C8', 'KETERANGAN');
        $sheet->setCellValue('D8', 'JENIS');
        $sheet->setCellValue('E8', 'NOMINAL');

        // Style untuk header tabel
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A8:E8')->applyFromArray($headerStyle);

        // Isi data transaksi
        $row = 9;
        foreach ($transactions as $trx) {
            $sheet->setCellValue("A{$row}", format_date($trx['payment_date'], 'd/m/Y'));
            $sheet->setCellValue("B{$row}", $trx['category_name'] ?? '-');
            $sheet->setCellValue("C{$row}", $trx['title']);
            $sheet->setCellValue("D{$row}", $trx['type'] == 'in' ? 'PEMASUKAN' : 'PENGELUARAN');
            $sheet->setCellValue("E{$row}", $trx['nominal']);

            // Style untuk jenis transaksi
            $jenisColor = $trx['type'] == 'in' ? '00B050' : 'FF0000';
            $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB($jenisColor);

            // Format nominal
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Auto size kolom
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border untuk tabel
        $tableStyle = [
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM],
                'inside' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $lastRow = count($transactions) + 8;
        $sheet->getStyle("A8:E{$lastRow}")->applyFromArray($tableStyle);

        // Set nama file
        $filename = $filename . '.xlsx';

        // Output file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
