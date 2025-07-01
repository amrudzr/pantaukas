<?php

/**
 * File: app/models/UserModel.php
 * Deskripsi: Menangani interaksi dengan database untuk data pengguna (user).
 * Diselaraskan dengan skema tabel 'user' yang diperbarui (menggunakan 'phone' sebagai unique identifier).
 */

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    /**
     * Mencari pengguna berdasarkan nomor telepon.
     * Digunakan untuk proses login.
     * @param string $phone Nomor telepon pengguna.
     * @return array|null Data pengguna jika ditemukan, null jika tidak.
     */
    public function getUserByPhone($phone)
    {
        $stmt = $this->db->prepare("SELECT id, name, phone, password, status FROM user WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    /**
     * Mencari pengguna berdasarkan nama (name).
     * Dapat digunakan untuk validasi unik nama, jika diperlukan (saat ini 'phone' yang UNIQUE).
     * @param string $name Nama pengguna.
     * @return array|null Data pengguna jika ditemukan, null jika tidak.
     */
    public function getUserByName($name)
    {
        $stmt = $this->db->prepare("SELECT id, name, phone, password, status FROM user WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    /**
     * Membuat pengguna baru di database.
     * @param string $name Nama pengguna.
     * @param string $phone Nomor telepon pengguna (harus unik).
     * @param string $hashedPassword Password yang sudah di-hash.
     * @return int|bool ID pengguna yang baru dibuat jika berhasil, false jika gagal.
     */
    public function createUser($name, $phone, $hashedPassword)
    {
        $stmt = $this->db->prepare("INSERT INTO user (name, phone, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $hashedPassword);

        if ($stmt->execute()) {
            $lastId = $this->db->insert_id;
            $stmt->close();
            return $lastId;
        } else {
            error_log("Error creating user: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Memperbarui waktu login terakhir pengguna.
     * @param int $userId ID pengguna.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("UPDATE user SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Error updating last login for user ID " . $userId . ": " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /** Temukan user berdasarkan ID */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /** Update data user */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE user SET name = ?, phone = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("sssi", $data['name'], $data['phone'], $data['password'], $id);

        if (!$stmt->execute()) {
            error_log("Error updating user: " . $stmt->error);
            throw new Exception('Gagal memperbarui data user');
        }

        return true;
    }

    /** Soft delete user */
    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE user SET status = 'deleted', deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Mendapatkan total jumlah user (termasuk yang dihapus/soft deleted)
     * @return int Jumlah total user
     */
    public function getCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    public function getCountByStatus($status)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user WHERE status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_row()[0];
    }

    public function getMonthlyCount($month, $year)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user 
        WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?");
        $stmt->bind_param("ss", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_row()[0];
    }

    public function getYearlyCount($year)
    {
        $stmt = $this->db->prepare("SELECT 
        SUM(MONTH(created_at) = 1) as jan,
        SUM(MONTH(created_at) = 2) as feb,
        SUM(MONTH(created_at) = 3) as mar,
        SUM(MONTH(created_at) = 4) as apr,
        SUM(MONTH(created_at) = 5) as may,
        SUM(MONTH(created_at) = 6) as jun,
        SUM(MONTH(created_at) = 7) as jul,
        SUM(MONTH(created_at) = 8) as aug,
        SUM(MONTH(created_at) = 9) as sep,
        SUM(MONTH(created_at) = 10) as oct,
        SUM(MONTH(created_at) = 11) as nov,
        SUM(MONTH(created_at) = 12) as december
        FROM user WHERE YEAR(created_at) = ?");
        $stmt->bind_param("s", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return array_values($data);
    }
}
