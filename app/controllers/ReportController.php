<?php
require_once APP_PATH . 'models/PaymentCashModel.php';
require_once APP_PATH . 'models/TypeCashModel.php';
require_once APP_PATH . 'models/PaymentFeeModel.php';
require_once APP_PATH . 'models/TypeFeeModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController
{
    private $paymentCashModel;
    private $paymentFeeModel;
    private $typeFeeModel;

    public function __construct()
    {
        $this->paymentCashModel = new PaymentCashModel();
        $this->paymentFeeModel = new PaymentFeeModel();
        $this->typeFeeModel = new TypeFeeModel();
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

        if ($jenis === 'kas') {
            $transactions = $this->getTransactionsByMonth($bulan, $tahun);
            $summary = $this->getSummaryByMonth($bulan, $tahun);

            view('report/view', [
                'transactions' => $transactions,
                'summary' => $summary,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'pageTitle' => 'Laporan Kas ' . $this->getMonthName($bulan) . ' ' . $tahun,
                'breadcrumbs' => [
                    ['label' => 'Laporan', 'url' => '/reports'],
                    ['label' => 'Detail Laporan', 'url' => '']
                ],
            ]);
        } elseif ($jenis === 'iuran') {
            $jenisIuranId = $_GET['jenis_iuran'] ?? null;
            if (!$jenisIuranId) {
                $_SESSION['flash'] = ['danger', 'Jenis iuran harus dipilih.'];
                redirect('/reports');
            }

            $details = $this->paymentFeeModel->getFeeDetails($jenisIuranId, $bulan, $tahun);
            $summary = $this->paymentFeeModel->getSummary($jenisIuranId, $bulan, $tahun);
            $feeType = $this->typeFeeModel->find($jenisIuranId);

            view('report/view_fee', [
                'details' => $details,
                'summary' => $summary,
                'feeType' => $feeType,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'pageTitle' => 'Laporan Iuran ' . $feeType['name'] . ' ' . $this->getMonthName($bulan) . ' ' . $tahun,
                'breadcrumbs' => [
                    ['label' => 'Laporan', 'url' => '/reports'],
                    ['label' => 'Detail Laporan Iuran', 'url' => '']
                ],
            ]);
        } else {
            $_SESSION['flash'] = ['danger', 'Jenis laporan tidak valid.'];
            redirect('/reports');
        }
    }

    /** GET /reports/export */
    public function export()
    {
        try {
            $jenis = $_GET['jenis'] ?? '';
            $bulan = $_GET['bulan'] ?? date('m');
            $tahun = $_GET['tahun'] ?? date('Y');

            if (!is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
                throw new Exception('Bulan harus antara 1-12');
            }

            if (!is_numeric($tahun) || $tahun < 2020 || $tahun > date('Y')) {
                throw new Exception('Tahun harus antara 2020-' . date('Y'));
            }

            if ($jenis === 'kas') {
                $transactions = $this->getTransactionsByMonth($bulan, $tahun);
                $summary = $this->getSummaryByMonth($bulan, $tahun);

                if (empty($transactions)) {
                    throw new Exception('Tidak ada data kas untuk periode ini');
                }

                $filename = 'Laporan_Kas_' . $this->getMonthName($bulan) . '_' . $tahun;
                $this->exportCashToExcel($transactions, $summary, $filename, $bulan, $tahun);
            } elseif ($jenis === 'iuran') {
                $jenisIuranId = $_GET['jenis_iuran'] ?? null;
                if (!$jenisIuranId) {
                    throw new Exception('Jenis iuran harus dipilih');
                }

                $details = $this->paymentFeeModel->getFeeDetails($jenisIuranId, $bulan, $tahun);
                $summary = $this->paymentFeeModel->getSummary($jenisIuranId, $bulan, $tahun);
                $feeType = $this->typeFeeModel->find($jenisIuranId);

                if (empty($details['members'])) {
                    throw new Exception('Tidak ada data iuran untuk periode ini');
                }

                $filename = 'Laporan_Iuran_' . $feeType['name'] . '_' . $this->getMonthName($bulan) . '_' . $tahun;
                $this->exportFeeToExcel($details, $summary, $filename, $bulan, $tahun, $feeType);
            } else {
                throw new Exception('Jenis laporan tidak valid');
            }
        } catch (Exception $e) {
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

    private function exportCashToExcel($transactions, $summary, $filename, $bulan, $tahun)
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

        // Output file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportFeeToExcel($details, $summary, $filename, $bulan, $tahun, $feeType)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul Laporan
        $sheet->setCellValue('A1', 'LAPORAN IURAN ' . strtoupper($feeType['name']));
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Periode
        $sheet->setCellValue('A2', 'Periode: ' . $this->getMonthName($bulan) . ' ' . $tahun);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ringkasan
        $sheet->setCellValue('A4', 'TARGET PER ANGGOTA');
        $sheet->setCellValue('B4', format_rupiah($summary['target_nominal']));
        $sheet->setCellValue('A5', 'TOTAL ANGGOTA');
        $sheet->setCellValue('B5', $summary['total_members']);
        $sheet->setCellValue('A6', 'TOTAL TARGET');
        $sheet->setCellValue('B6', format_rupiah($summary['total_target']));
        $sheet->setCellValue('A7', 'TOTAL TERBAYAR');
        $sheet->setCellValue('B7', format_rupiah($summary['total_paid']));
        $sheet->setCellValue('A8', 'BELUM BAYAR');
        $sheet->setCellValue('B8', $summary['total_unpaid']);
        $sheet->setCellValue('A9', 'TANGGUNGAN');
        $sheet->setCellValue('B9', $summary['total_debt']);

        // Style untuk ringkasan
        $sheet->getStyle('A4:A9')->getFont()->setBold(true);
        $sheet->getStyle('B4:B9')->getNumberFormat()->setFormatCode('#,##0');

        // Header Tabel
        $sheet->setCellValue('A11', 'NO');
        $sheet->setCellValue('B11', 'NAMA ANGGOTA');
        $sheet->setCellValue('C11', 'NOMOR HP');
        $sheet->setCellValue('D11', 'STATUS');
        $sheet->setCellValue('E11', 'NOMINAL');
        $sheet->setCellValue('F11', 'KETERANGAN');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A11:F11')->applyFromArray($headerStyle);

        // Isi data
        $row = 12;
        $no = 1;
        foreach ($details['members'] as $member) {
            $status = '';
            $statusColor = '000000';

            // Konversi status ke Bahasa Indonesia
            switch ($member['payment_status']) {
                case 'paid':
                    $status = 'LUNAS';
                    $statusColor = '00B050'; // Hijau
                    break;
                case 'unpaid':
                    $status = 'BELUM BAYAR';
                    $statusColor = 'FF0000'; // Merah
                    break;
                case 'debt':
                    $status = 'TANGGUNGAN';
                    $statusColor = 'FFC000'; // Kuning/Oranye
                    break;
                default:
                    $status = strtoupper($member['payment_status']);
            }

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $member['member_name']);
            $sheet->setCellValue("C{$row}", $member['member_phone']);
            $sheet->setCellValue("D{$row}", $status);
            $sheet->setCellValue("E{$row}", $member['payment_nominal']);
            $sheet->setCellValue("F{$row}", $member['payment_notes'] ?? '-');

            // Warna status
            $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB($statusColor);

            // Format nominal
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Auto size kolom
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border tabel
        $tableStyle = [
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM],
                'inside' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $lastRow = count($details['members']) + 11;
        $sheet->getStyle("A11:F{$lastRow}")->applyFromArray($tableStyle);

        // Output file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
