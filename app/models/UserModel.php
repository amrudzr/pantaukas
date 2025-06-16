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
}
