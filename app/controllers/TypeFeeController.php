<?php
require_once APP_PATH . 'models/TypeFeeModel.php';

class TypeFeeController
{
    private $model;

    public function __construct()
    {
        $this->model = new TypeFeeModel();
    }

    /** GET /fee/types */
    public function index()
    {
        $search = trim($_GET['search'] ?? '');
        $type_fees = $this->model->all($search);

        view('type_fee/index', [
            'type_fees' => $type_fees,
            'pageTitle' => 'Data Jenis Iuran',
            'breadcrumbs' => [
                ['label' => 'Jenis Iuran', 'url' => '/fee/types']
            ],
        ]);
    }

    /** ANY /fee/types/create */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->create(
                    (int)$_SESSION['user_id'],
                    trim($_POST['name']),
                    (int)$_POST['nominal'],
                    $_POST['duration'],
                    $_POST['description'] ?: null
                );
                $_SESSION['flash'] = ['success', 'Jenis iuran berhasil ditambah.'];
                redirect('/fee/types');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('type_fee/create', [
            'pageTitle' => 'Tambah Jenis Iuran',
            'breadcrumbs' => [
                ['label' => 'Jenis Iuran', 'url' => '/fee/types'],
                ['label' => 'Tambah', 'url' => '/fee/types/create']
            ],
        ]);
    }

    /** ANY /fee/types/{id}/edit */
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
                    (int)$_POST['nominal'],
                    $_POST['duration'],
                    $_POST['description'] ?: null
                );
                $_SESSION['flash'] = ['success', 'Jenis iuran berhasil diperbarui.'];
                redirect('/fee/types');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('type_fee/edit', [
            'data' => $data,
            'pageTitle' => 'Edit Jenis Iuran',
            'breadcrumbs' => [
                ['label' => 'Jenis Iuran', 'url' => '/fee/types'],
                ['label' => 'Edit', 'url' => '/fee/types/' . $id . '/edit']
            ],
        ]);
    }

    /** POST /fee/types/{id}/delete */
    public function delete($id)
    {
        $data = $this->model->find($id);

        if ($data && $data['id_user'] === null) {
            $_SESSION['flash'] = ['danger', 'Jenis iuran sistem tidak bisa dihapus.'];
            redirect('/fee/types');
        }

        if ($this->model->delete($id)) {
            $_SESSION['flash'] = ['success', 'Jenis iuran dihapus.'];
        } else {
            $_SESSION['flash'] = ['danger', 'Gagal menghapus jenis iuran.'];
        }

        redirect('/fee/types');
    }
}
