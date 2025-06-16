<?php
/**
 * File: app/models/ExampleModel.php
 * Deskripsi:
 *  Model ini hanya sebagai **contoh edukasi** agar developer memahami struktur dasar Model.
 *  Tidak digunakan untuk eksekusi query sungguhan.
 * 
 * Catatan:
 * - Tidak ada tabel `examples` di database.
 * - Query SQL ditulis sebagai komentar hanya untuk pembelajaran.
 */

class ExampleModel
{
    private $db;

    public function __construct()
    {
        // Koneksi database (simulasi, tidak digunakan di model dummy ini)
        $this->db = getDbConnection(); // fungsi helper dari config/Database.php
    }

    /**
     * Mendapatkan semua data (simulasi).
     * Jika tersambung ke database:
     * 
     * Contoh Query:
     * $stmt = $this->db->prepare("SELECT * FROM examples WHERE nama LIKE ?");
     * $searchTerm = '%' . $keyword . '%';
     * $stmt->bind_param("s", $searchTerm);
     * $stmt->execute();
     * return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
     */
    public function all($keyword = '')
    {
        $dummy = [
            ['id' => 1, 'nama' => 'Ahmad Surya', 'alamat' => 'Jl. Melati', 'status' => 'Aktif'],
            ['id' => 2, 'nama' => 'Budi Santoso', 'alamat' => 'Jl. Mawar', 'status' => 'Pasif'],
            ['id' => 3, 'nama' => 'Citra Ayu', 'alamat' => 'Jl. Anggrek', 'status' => 'Keluar'],
        ];

        if ($keyword !== '') {
            return array_filter($dummy, function ($item) use ($keyword) {
                return stripos($item['nama'], $keyword) !== false || stripos($item['alamat'], $keyword) !== false;
            });
        }

        return $dummy;
    }

    /**
     * Mencari data berdasarkan ID (simulasi).
     * 
     * Contoh Query:
     * $stmt = $this->db->prepare("SELECT * FROM examples WHERE id = ?");
     * $stmt->bind_param("i", $id);
     * $stmt->execute();
     * return $stmt->get_result()->fetch_assoc();
     */
    public function find($id)
    {
        return [
            'id'     => $id,
            'nama'   => 'Nama Dummy',
            'alamat' => 'Alamat Dummy',
            'status' => 'Aktif'
        ];
    }

    /**
     * Simulasi menyimpan data (CREATE).
     * 
     * Contoh Query:
     * $stmt = $this->db->prepare("INSERT INTO examples (nama, alamat, status) VALUES (?, ?, ?)");
     * $stmt->bind_param("sss", $nama, $alamat, $status);
     * if ($stmt->execute()) {
     *     return $this->db->insert_id;
     * }
     */
    public function create($nama, $alamat, $status)
    {
        // Simulasi berhasil insert
        return rand(100, 999); // Random ID sebagai ilustrasi
    }

    /**
     * Simulasi update data (UPDATE).
     * 
     * Contoh Query:
     * $stmt = $this->db->prepare("UPDATE examples SET nama = ?, alamat = ?, status = ? WHERE id = ?");
     * $stmt->bind_param("sssi", $nama, $alamat, $status, $id);
     * return $stmt->execute();
     */
    public function update($id, $nama, $alamat, $status)
    {
        return true; // Simulasi sukses
    }

    /**
     * Simulasi hapus data (DELETE).
     * 
     * Contoh Query:
     * $stmt = $this->db->prepare("DELETE FROM examples WHERE id = ?");
     * $stmt->bind_param("i", $id);
     * return $stmt->execute();
     */
    public function delete($id)
    {
        return true; // Simulasi sukses
    }
}
