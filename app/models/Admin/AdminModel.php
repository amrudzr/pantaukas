<?php

/**
 * File: app/models/AdminModel.php
 * Deskripsi: Menangani interaksi dengan database untuk data admin.
 * Mendukung operasi CRUD dan manajemen akses untuk role superadmin, admin, dan operator.
 */

class AdminModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    /**
     * Mencari admin berdasarkan email.
     * Digunakan untuk proses login admin.
     * @param string $email Email admin.
     * @return array|null Data admin jika ditemukan, null jika tidak.
     */
    public function getAdminByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT id, name, email, password, role, status FROM admin WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
        return $admin;
    }

    /**
     * Membuat admin baru di database.
     * @param string $name Nama lengkap admin.
     * @param string $email Email admin (harus unik).
     * @param string $hashedPassword Password yang sudah di-hash.
     * @param string $role Role admin (superadmin|admin|operator).
     * @return int|bool ID admin yang baru dibuat jika berhasil, false jika gagal.
     */
    public function createAdmin($name, $email, $hashedPassword, $role = 'operator')
    {
        $stmt = $this->db->prepare("INSERT INTO admin (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            $lastId = $this->db->insert_id;
            $stmt->close();
            return $lastId;
        } else {
            error_log("Error creating admin: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Memperbarui waktu login terakhir admin.
     * @param int $adminId ID admin.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateLastLogin($adminId)
    {
        $stmt = $this->db->prepare("UPDATE admin SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $adminId);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Error updating last login for admin ID " . $adminId . ": " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Temukan admin berdasarkan ID
     * @param int $id ID admin
     * @return array|null Data admin jika ditemukan
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE id = ? AND deleted_at IS NULL");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Update data admin
     * @param int $id ID admin
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil
     * @throws Exception Jika gagal update
     */
    public function update($id, $data)
    {
        $query = "UPDATE admin SET name = ?, email = ?, role = ?, status = ?, updated_at = CURRENT_TIMESTAMP";
        $types = "ssss";
        $params = [$data['name'], $data['email'], $data['role'], $data['status']];

        // Jika password diupdate
        if (!empty($data['password'])) {
            $query .= ", password = ?";
            $types .= "s";
            $params[] = $data['password'];
        }

        $query .= " WHERE id = ?";
        $types .= "i";
        $params[] = $id;

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            error_log("Error updating admin: " . $stmt->error);
            throw new Exception('Gagal memperbarui data admin');
        }

        return true;
    }

    /**
     * Soft delete admin
     * @param int $id ID admin
     * @return bool True jika berhasil
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE admin SET status = 'inactive', deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Mendapatkan semua admin (kecuali yang dihapus)
     * @param string $role Filter berdasarkan role (opsional)
     * @return array List admin
     */
    public function getAll($role = null)
    {
        $query = "SELECT * FROM admin WHERE deleted_at IS NULL";
        $params = [];
        $types = "";

        if ($role) {
            $query .= " AND role = ?";
            $types = "s";
            $params[] = $role;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengubah status admin
     * @param int $id ID admin
     * @param string $status Status baru (active|inactive|suspended)
     * @return bool True jika berhasil
     */
    public function changeStatus($id, $status)
    {
        $validStatuses = ['active', 'inactive', 'suspended'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Status tidak valid');
        }

        $stmt = $this->db->prepare("UPDATE admin SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    /**
     * Memeriksa apakah email sudah digunakan
     * @param string $email Email yang akan dicek
     * @param int $excludeId ID admin yang akan di-exclude (untuk update)
     * @return bool True jika email sudah digunakan
     */
    public function isEmailExists($email, $excludeId = null)
    {
        $query = "SELECT COUNT(*) as count FROM admin WHERE email = ? AND deleted_at IS NULL";
        $params = [$email];
        $types = "s";

        if ($excludeId) {
            $query .= " AND id != ?";
            $types .= "i";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    /**
     * Mendapatkan total jumlah admin (tidak termasuk yang dihapus)
     * @return int Jumlah admin
     */
    public function getCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM admin WHERE deleted_at IS NULL");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    /**
     * Mendapatkan jumlah admin berdasarkan status
     * @param string $status Status yang dicari (active|inactive|suspended)
     * @return int Jumlah admin dengan status tersebut
     */
    public function getCountByStatus($status)
    {
        $validStatuses = ['active', 'inactive', 'suspended'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Status tidak valid');
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM admin WHERE status = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    /**
     * Mendapatkan jumlah admin yang dibuat pada bulan tertentu
     * @param int $month Bulan (1-12)
     * @param int $year Tahun
     * @return int Jumlah admin
     */
    public function getMonthlyCount($month, $year)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM admin 
                               WHERE MONTH(created_at) = ? 
                               AND YEAR(created_at) = ?
                               AND deleted_at IS NULL");
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    /**
     * Mendapatkan jumlah admin yang dibuat pada tahun tertentu
     * @param int $year Tahun
     * @return int Jumlah admin
     */
    public function getYearlyCount($year)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM admin 
                               WHERE YEAR(created_at) = ?
                               AND deleted_at IS NULL");
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
}
