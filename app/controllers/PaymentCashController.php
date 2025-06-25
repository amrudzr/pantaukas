<?php
require_once APP_PATH . 'models/PaymentCashModel.php';
require_once APP_PATH . 'models/TypeCashModel.php'; // Untuk kategori kas

class PaymentCashController
{
    private $model;

    public function __construct()
    {
        $this->model = new PaymentCashModel();
    }

    /** GET /cash */
    public function index()
    {
        $search = trim($_GET['search'] ?? '');

        $month = $_GET['bulan'] ?? date('m');
        $year  = $_GET['tahun'] ?? date('Y');

        $cash = $this->model->all($search);

        $totalPemasukan = $this->model->sumByTypeAndDate('in', $month, $year);
        $totalPengeluaran = $this->model->sumByTypeAndDate('out', $month, $year);
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        view('payment_cash/index', [
            'cash' => $cash,
            'pageTitle' => 'Data Kas',
            'breadcrumbs' => [
                ['label' => 'Kas', 'url' => '/cash']
            ],
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoKas' => $saldoKas,
        ]);
    }

    /** ANY /cash/create */
    public function create()
    {
        $typeModel = new TypeCashModel();
        $typeCashList = $typeModel->all(); // Ambil semua kategori kas

        if (is_post()) {
            try {
                $attachment = null;

                if (!empty($_POST['attachment_url'])) {
                    $attachment = $_POST['attachment_url'];
                } elseif (!empty($_FILES['attachment_file']['name'])) {
                    $attachment = upload_file($_FILES['attachment_file'], 'kas/');
                } elseif (!empty($_FILES['attachment_camera']['name'])) {
                    $attachment = upload_file($_FILES['attachment_camera'], 'kas/');
                }

                $this->model->create([
                    'id_type_cash'  => $_POST['id_type_cash'] ?? null,
                    'id_user'       => $_SESSION['user_id'],
                    'title'         => trim($_POST['title']),
                    'payment_date'  => $_POST['payment_date'],
                    'nominal'       => intval($_POST['nominal']),
                    'type'          => $_POST['type'] ?? 'in',
                    'attachment'    => $attachment,
                    'notes'         => $_POST['notes'] ?? null,
                ]);

                $_SESSION['flash'] = ['success', 'Transaksi kas berhasil ditambahkan.'];
                redirect('/cash');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('payment_cash/create', [
            'typeCashList' => $typeCashList,
            'pageTitle' => 'Buat Transaksi',
            'breadcrumbs' => [
                ['label' => 'Kas', 'url' => '/cash'],
                ['label' => 'Buat Transaksi', 'url' => '/cash/create']
            ],
        ]);
    }

    /** ANY /cash/{id}/edit */
    public function edit($id)
    {
        $typeModel = new TypeCashModel();
        $typeCashList = $typeModel->all();

        $cash = $this->model->find($id);
        if (!$cash) {
            $_SESSION['flash'] = ['danger', 'Transaksi tidak ditemukan.'];
            redirect('/cash');
        }

        if (is_post()) {
            try {
                $attachment = $cash['attachment']; // default: tetap pakai file lama
                $isOldAttachmentFile = !filter_var($attachment, FILTER_VALIDATE_URL) && file_exists($_SERVER['DOCUMENT_ROOT'] . $attachment);

                // Ganti URL
                if (!empty($_POST['attachment_url'])) {
                    if ($isOldAttachmentFile) {
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $attachment); // hapus file lama jika ada
                    }
                    $attachment = $_POST['attachment_url'];
                }
                // Ganti file upload
                elseif (!empty($_FILES['attachment_file']['name'])) {
                    if ($isOldAttachmentFile) {
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $attachment);
                    }
                    $attachment = upload_file($_FILES['attachment_file'], 'kas/');
                }
                // Ganti kamera
                elseif (!empty($_FILES['attachment_camera']['name'])) {
                    if ($isOldAttachmentFile) {
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $attachment);
                    }
                    $attachment = upload_file($_FILES['attachment_camera'], 'kas/');
                }

                $this->model->update($id, [
                    'id_type_cash'  => $_POST['id_type_cash'] ?? null,
                    'title'         => trim($_POST['title']),
                    'payment_date'  => $_POST['payment_date'],
                    'nominal'       => intval($_POST['nominal']),
                    'type'          => $_POST['type'] ?? 'in',
                    'attachment'    => $attachment,
                    'notes'         => $_POST['notes'] ?? null,
                ]);

                $_SESSION['flash'] = ['success', 'Transaksi kas berhasil diperbarui.'];
                redirect('/cash');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('payment_cash/edit', [
            'cash' => $cash,
            'typeCashList' => $typeCashList,
            'pageTitle' => 'Edit Transaksi',
            'breadcrumbs' => [
                ['label' => 'Kas', 'url' => '/cash'],
                ['label' => 'Edit Transaksi', 'url' => '/cash/' . $id . '/edit'],
            ],
        ]);
    }

    /** POST /cash/{id}/delete */
    public function delete($id)
    {
        $cash = $this->model->find($id);
        if ($cash) {
            $attachment = $cash['attachment'];
            if (!filter_var($attachment, FILTER_VALIDATE_URL) && file_exists($_SERVER['DOCUMENT_ROOT'] . $attachment)) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . $attachment); // hapus file lampiran jika bukan URL
            }
        }

        $this->model->delete($id);
        $_SESSION['flash'] = ['success', 'Data transaksi dihapus.'];
        redirect('/cash');
    }
}
