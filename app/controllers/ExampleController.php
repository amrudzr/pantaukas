<?php
/**
 * File: app/controllers/ExampleController.php
 * Deskripsi: Contoh controller dummy untuk resource "examples"
 * Tujuannya adalah edukasi, agar developer pemula memahami alur routing, tampilan, dan manipulasi data dummy.
 */

class ExampleController
{
    /**
     * Menampilkan daftar data (halaman index)
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';

        // Dummy data (biasanya didapat dari database)
        $data = [
            ['id' => 1, 'nama' => 'Ahmad Surya', 'alamat' => 'Jl. Melati', 'status' => 'Aktif'],
            ['id' => 2, 'nama' => 'Budi Santoso', 'alamat' => 'Jl. Mawar', 'status' => 'Pasif'],
            ['id' => 3, 'nama' => 'Citra Ayu', 'alamat' => 'Jl. Anggrek', 'status' => 'Keluar'],
        ];

        // Simulasi filter pencarian (biasanya dilakukan di query SQL)
        if ($search !== '') {
            $data = array_filter($data, function ($item) use ($search) {
                return stripos($item['nama'], $search) !== false || stripos($item['alamat'], $search) !== false;
            });
        }

        // Kirim data ke view
        $pageTitle = "Example";
        $breadcrumbs = [
            ['label' => 'Example', 'url' => '/examples']
        ];
        $contentView = 'example/index.php';
        include APP_PATH . 'views/layout/app.php';
    }

    /**
     * Form tambah data (GET) / Simpan data baru (POST)
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama   = $_POST['nama'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $status = $_POST['status'] ?? '';

            // Simulasi menyimpan ke database:
            // $stmt = $db->prepare("INSERT INTO examples (nama, alamat, status) VALUES (?, ?, ?)");
            // $stmt->execute([$nama, $alamat, $status]);

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
     * Form edit data (GET) / Proses update (POST)
     */
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama   = $_POST['nama'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $status = $_POST['status'] ?? '';

            // Simulasi update ke database:
            // $stmt = $db->prepare("UPDATE examples SET nama = ?, alamat = ?, status = ? WHERE id = ?");
            // $stmt->execute([$nama, $alamat, $status, $id]);

            header('Location: /examples');
            exit;
        }

        // Simulasi ambil data dari DB:
        // $stmt = $db->prepare("SELECT * FROM examples WHERE id = ?");
        // $stmt->execute([$id]);
        // $data = $stmt->fetch();

        $data = [
            'id' => $id,
            'nama' => 'Nama Dummy',
            'alamat' => 'Alamat Dummy',
            'status' => 'Aktif'
        ];

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
     */
    public function delete($id)
    {
        // Simulasi hapus dari database:
        // $stmt = $db->prepare("DELETE FROM examples WHERE id = ?");
        // $stmt->execute([$id]);

        header('Location: /examples');
        exit;
    }
}
