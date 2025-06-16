<?php

/**
 * File: app/controllers/ExampleController.php
 * Deskripsi: Contoh controller dummy untuk resource "examples".
 * Tujuannya adalah edukasi — agar developer pemula memahami alur routing, tampilan, dan manipulasi data.
 */

require_once APP_PATH . 'models/ExampleModel.php'; // pastikan model dimuat

class ExampleController
{
    private $model;

    public function __construct()
    {
        $this->model = new ExampleModel();
    }

    /**
     * Menampilkan daftar data (halaman index)
     * URL: /examples
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $examples = $this->model->all($search);

        $pageTitle = "Data Contoh";
        $breadcrumbs = [
            ['label' => 'Example', 'url' => '/examples']
        ];
        $contentView = 'example/index.php';

        include APP_PATH . 'views/layout/app.php';
    }

    /**
     * Menampilkan form tambah data (GET) atau menyimpan data baru (POST)
     * URL: /examples/create
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $status = $_POST['status'] ?? '';

            $this->model->create($nama, $alamat, $status);

            header('Location: /examples');
            exit;
        }

        $pageTitle = "Tambah Data";
        $breadcrumbs = [
            ['label' => 'Example', 'url' => '/examples'],
            ['label' => 'Tambah', 'url' => '']
        ];
        $contentView = 'example/create.php';

        include APP_PATH . 'views/layout/app.php';
    }

    /**
     * Menampilkan form edit data (GET) atau menyimpan perubahan (POST)
     * URL: /examples/edit/{id}
     */
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $status = $_POST['status'] ?? '';

            $this->model->update($id, $nama, $alamat, $status);

            header('Location: /examples');
            exit;
        }

        $data = $this->model->find($id);

        $pageTitle = "Edit Data";
        $breadcrumbs = [
            ['label' => 'Example', 'url' => '/examples'],
            ['label' => 'Edit', 'url' => '']
        ];
        $contentView = 'example/edit.php';

        include APP_PATH . 'views/layout/app.php';
    }

    /**
     * Menghapus data
     * URL: /examples/delete/{id}
     */
    public function delete($id)
    {
        $this->model->delete($id);
        header('Location: /examples');
        exit;
    }
}
