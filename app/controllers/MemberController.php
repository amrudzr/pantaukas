<?php
require_once APP_PATH . 'models/MemberModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MemberController
{
    private $model;

    public function __construct()
    {
        $this->model = new MemberModel();
    }

    /** GET /members */
    public function index()
    {
        $search   = trim($_GET['search'] ?? '');
        $members  = $this->model->all($search);

        view('member/index', [
            'members' => $members,
            'pageTitle' => 'Data Anggota',
            'breadcrumbs' => [
                ['label' => 'Anggota', 'url' => '/members']
            ],
        ]);
    }

    /** ANY /members/create */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->create(
                    (int)$_SESSION['user_id'],
                    trim($_POST['name']),
                    $_POST['phone'] ?: null,
                    $_POST['address'] ?: null,
                    $_POST['status'] ?? 'active'
                );
                $_SESSION['flash'] = ['success', 'Anggota berhasil ditambah.'];
                header('Location: /members');
                exit;
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('member/create', [
            'pageTitle' => 'Tambah Anggota',
            'breadcrumbs' => [
                ['label' => 'Anggota', 'url' => '/members'],
                ['label' => 'Tambah',  'url' => '/members/create']
            ],
        ]);
    }

    /** ANY /members/{id}/edit */
    public function edit($id)
    {
        $data = $this->model->find($id);
        if (!$data) {
            header("HTTP/1.0 403 Forbidden");
            echo "Data tidak ditemukan atau akses ditolak.";
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->update(
                    $id,
                    trim($_POST['name']),
                    $_POST['phone'] ?: null,
                    $_POST['address'] ?: null,
                    $_POST['status'] ?? 'active'
                );
                $_SESSION['flash'] = ['success', 'Member berhasil diperbarui.'];
                header('Location: /members');
                exit;
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('member/edit', [
            'data' => $data,
            'pageTitle' => 'Edit Anggota',
            'breadcrumbs' => [
                ['label' => 'Anggota', 'url' => '/members'],
                ['label' => 'Edit',  'url' => '/members/'.$id.'/edit']
            ],
        ]);
    }

    /** POST /members/{id}/delete */
    public function delete($id)
    {
        $this->model->delete($id);
        $_SESSION['flash'] = ['success', 'Member dihapus.'];
        header('Location: /members');
        exit;
    }

    /** GET /members/template */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Nomor Telepon');
        $sheet->setCellValue('C1', 'Alamat');
        $sheet->setCellValue('D1', 'Status (aktif, pasif, nonaktif)');

        // Contoh data
        $sheet->setCellValue('A2', 'Contoh: Budi');
        $sheet->setCellValue('B2', '081234567890');
        $sheet->setCellValue('C2', 'Alamat Contoh');
        $sheet->setCellValue('D2', 'aktif');

        $sheet->setCellValue('A3', 'Arif');
        $sheet->setCellValue('B3', '081334567890');
        $sheet->setCellValue('C3', 'Jakarta');
        $sheet->setCellValue('D3', 'aktif');

        // Keterangan status
        $sheet->setCellValue('F1', 'Keterangan Status:');
        $sheet->setCellValue('F2', 'aktif = Anggota aktif dan terlibat dalam kegiatan');
        $sheet->setCellValue('F3', 'pasif = Anggota sementara tidak aktif');
        $sheet->setCellValue('F4', 'nonaktif = Anggota keluar secara resmi');

        // Download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_anggota.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /** GET /members/export */
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'No HP');
        $sheet->setCellValue('C1', 'Alamat');
        $sheet->setCellValue('D1', 'Status');

        // Ambil data dari database
        $members = $this->model->all();

        $rowNum = 2;
        foreach ($members as $member) {
            $sheet->setCellValue("A{$rowNum}", $member['name']);
            $sheet->setCellValue("B{$rowNum}", $member['phone']);
            $sheet->setCellValue("C{$rowNum}", $member['address']);
            $sheet->setCellValue("D{$rowNum}", $this->mapStatusToIndo($member['status']));
            $rowNum++;
        }

        // Set nama file download
        $tanggal = date('Y-m-d_H-i-s'); // format: 2025-06-24_16-45-00
        $filename = 'data_anggota_'.$tanggal.'.xlsx';

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /** POST /members/import */
    public function import()
    {
        if (!isset($_FILES['excel']) || $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = ['danger', 'Gagal upload file. Periksa kembali format file'];
            header('Location: /members');
            exit;
        }

        $fileTmpPath = $_FILES['excel']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($fileTmpPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            // Lewati baris header (baris ke-1)
            unset($rows[1]);

            $imported = 0;
            foreach ($rows as $row) {
                $name    = trim($row['A'] ?? '');
                $phone   = trim($row['B'] ?? '');
                $address = trim($row['C'] ?? '');
                $status  = $this->mapStatus(trim($row['D'] ?? ''));

                // Lewati jika nama atau status tidak valid
                if ($name === '' || !$status) {
                    continue;
                }

                $this->model->create(
                    (int)$_SESSION['user_id'],
                    $name,
                    $phone,
                    $address,
                    $status
                );

                $imported++;
            }

            $_SESSION['flash'] = ['success', "Berhasil mengimpor $imported data anggota."];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['danger', 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage()];
        }

        header('Location: /members');
        exit;
    }

    private function mapStatus($status)
    {
        return match (strtolower($status)) {
            'aktif'     => 'active',
            'pasif'     => 'passive',
            'nonaktif', 'tidak aktif' => 'inactive',
            'active', 'passive', 'inactive' => strtolower($status),
            default     => null
        };
    }

    private function mapStatusToIndo($status)
    {
        return match (strtolower($status)) {
            'active'   => 'Aktif',
            'passive'  => 'Pasif',
            'inactive' => 'Nonaktif',
            default    => ''
        };
    }
}
