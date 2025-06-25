<?php
require_once APP_PATH . 'models/TypeCashModel.php';

class TypeCashController
{
    private $model;

    public function __construct()
    {
        $this->model = new TypeCashModel();
    }

    /** GET /cash/categories */
    public function index()
    {
        $search = trim($_GET['search'] ?? '');
        $type_cash = $this->model->all($search);

        view('payment_cash/type_cash/index', [
            'type_cash' => $type_cash,
            'pageTitle' => 'Data Kategori Transaksi',
            'breadcrumbs' => [
                ['label' => 'Kas', 'url' => '/cash'],
                ['label' => 'Kategori Transaksi', 'url' => '/cash/categoires']
            ],
        ]);
    }

    /** ANY /cash/categories/create */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->create(
                    (int)$_SESSION['user_id'],
                    trim($_POST['name']),
                    $_POST['description'] ?: null
                );
                $_SESSION['flash'] = ['success', 'Kategori berhasil ditambah.'];
                redirect('/cash/categories');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('payment_cash/type_cash/create', [
            'pageTitle' => 'Tambah Kategori Transaksi',
            'breadcrumbs' => [
                ['label' => 'Kas', 'url' => '/cash'],
                ['label' => 'Kategori Transaksi', 'url' => '/cash/categories'],
                ['label' => 'Tambah', 'url' => '/cash/categories/create']
            ],
        ]);
    }

    /** ANY /cash/categories/{id}/edit */
    public function edit($id)
    {
        $data = $this->model->find($id);
        if (!$data) {
            header("HTTP/1.0 404 Not Found");
            echo "Data tidak ditemukan.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->update(
                    $id,
                    trim($_POST['name']),
                    $_POST['description'] ?: null
                );
                $_SESSION['flash'] = ['success', 'Kategori berhasil diperbarui.'];
                redirect('/cash/categories');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('payment_cash/type_cash/edit', [
            'data' => $data,
            'pageTitle' => 'Edit Kategori Transaksi',
            'breadcrumbs' => [
                ['label' => 'Kas', 'url' => '/cash'],
                ['label' => 'Kategori Transaksi', 'url' => '/cash/categories'],
                ['label' => 'Edit', 'url' => '/cash/categories/'.$id.'/edit']
            ],
        ]);
    }

    /** POST /cash/categories/{id}/delete */
    public function delete($id)
    {
        $data = $this->model->find($id);

        if ($data && $data['id_user'] === null) {
            $_SESSION['flash'] = ['danger', 'Kategori sistem tidak bisa dihapus.'];
            redirect('/cash/categories');
        }

        if ($this->model->delete($id)) {
            $_SESSION['flash'] = ['success', 'Kategori dihapus.'];
        } else {
            $_SESSION['flash'] = ['danger', 'Gagal menghapus kategori.'];
        }

        redirect('/cash/categories');
    }
}
