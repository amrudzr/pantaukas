<?php

/**
 * File: app/controllers/AuthController.php
 * Deskripsi: Menangani logika registrasi, login, dan logout pengguna.
 * Diperbarui untuk menggunakan skema tabel 'user' yang baru (name, phone, tanpa email).
 */

// Pastikan UserModel dimuat untuk berinteraksi dengan tabel user di database.
require_once APP_PATH . 'models/UserModel.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Menampilkan form registrasi atau memproses data POST untuk registrasi pengguna baru.
     */
    public function register()
    {
        $pageTitle = "Registrasi Akun Baru";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');       // Mengambil nama pengguna
            $phone = trim($_POST['phone'] ?? '');     // Mengambil nomor telepon
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validasi input
            if (empty($name) || empty($phone) || empty($password) || empty($confirmPassword)) {
                echo "<script>alert('Semua kolom harus diisi!');</script>";
            } elseif ($password !== $confirmPassword) {
                echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
            } elseif (strlen($password) < 6) {
                echo "<script>alert('Password minimal 6 karakter!');</script>";
            } elseif ($this->userModel->getUserByPhone($phone)) {
                // Cek apakah nomor telepon sudah terdaftar (phone adalah UNIQUE)
                echo "<script>alert('Nomor telepon sudah terdaftar!');</script>";
            } elseif ($this->userModel->getUserByName($name)) {
                // Opsional: Cek apakah nama pengguna sudah digunakan (jika nama juga diharapkan unik)
                // Jika 'name' tidak UNIQUE di DB, ini hanya untuk user-friendliness
                echo "<script>alert('Nama pengguna sudah digunakan!');</script>";
            } else {
                // Jika semua validasi lolos, hash password sebelum menyimpannya ke database.
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Buat pengguna baru melalui UserModel
                if ($this->userModel->createUser($name, $phone, $hashedPassword)) {
                    echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='/login';</script>";
                    exit();
                } else {
                    echo "<script>alert('Gagal registrasi. Silakan coba lagi.');</script>";
                }
            }
        }
        // Muat tampilan registrasi tanpa layout app.php
        require_once APP_PATH . 'views/layout/header.php';
        require_once APP_PATH . 'views/register.php';
        require_once APP_PATH . 'views/layout/footer.php';
    }

    /**
     * Menampilkan form login atau memproses data POST untuk login pengguna.
     */
    public function login()
    {
        $pageTitle = "Login";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phone = trim($_POST['phone'] ?? ''); // Mengambil nomor telepon untuk login
            $password = $_POST['password'] ?? '';

            // Mencari pengguna berdasarkan nomor telepon
            $user = $this->userModel->getUserByPhone($phone);

            // Verifikasi pengguna, password, dan status
            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] === 'active') {
                    // Login berhasil: Simpan informasi pengguna di sesi
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name']; // Menggunakan 'name'
                    $_SESSION['phone'] = $user['phone']; // Menggunakan 'phone'

                    // Perbarui waktu login terakhir
                    $this->userModel->updateLastLogin($user['id']);

                    // Redirect ke halaman utama atau dashboard setelah login berhasil
                    header('Location: /dashboard');
                    exit();
                } else {
                    echo "<script>alert('Akun Anda " . htmlspecialchars($user['status']) . ". Silakan hubungi administrator.');</script>";
                }
            } else {
                // Jika nomor telepon atau password salah
                echo "<script>alert('Nomor telepon atau password salah!');</script>";
            }
        }
        // Muat tampilan login tanpa layout app.php
        require_once APP_PATH . 'views/layout/header.php';
        require_once APP_PATH . 'views/login.php';
        require_once APP_PATH . 'views/layout/footer.php';
    }

    /**
     * Menghapus sesi pengguna dan mengarahkan ke halaman login.
     */
    public function logout()
    {
        // Hancurkan semua variabel sesi
        session_unset();
        // Hancurkan sesi itu sendiri (menghapus file sesi dari server)
        session_destroy();

        // Redirect ke halaman login setelah logout
        header('Location: /login');
        exit();
    }
}
