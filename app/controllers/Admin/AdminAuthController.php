<?php

/**
 * File: app/controllers/admin/AuthController.php
 * Deskripsi: Menangani logika login dan logout admin dengan sistem role-based.
 */

require_once APP_PATH . 'models/Admin/AdminModel.php';

class AdminAuthController
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    /**
     * Menampilkan form login admin atau memproses data POST.
     */
    public function login()
    {
        $pageTitle = "Admin Login";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $admin = $this->adminModel->getAdminByEmail($email);

            // Verifikasi admin, password, dan status
            if ($admin && password_verify($password, $admin['password'])) {
                if ($admin['status'] === 'active') {
                    // Simpan informasi admin di sesi
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_role'] = $admin['role'];

                    // Perbarui waktu login terakhir
                    $this->adminModel->updateLastLogin($admin['id']);

                    // Redirect berdasarkan role
                    $redirectUrl = $this->getRedirectUrlByRole($admin['role']);
                    header("Location: $redirectUrl");
                    exit();
                } else {
                    $this->showError("Akun Anda " . htmlspecialchars($admin['status']) . ". Silakan hubungi superadmin.");
                }
            } else {
                $this->showError("Email atau password salah!");
            }
        }

        // Muat tampilan login admin (layout khusus)
        $this->renderAdminLoginView();
    }

    /**
     * Menghapus sesi admin dan mengarahkan ke halaman login admin.
     */
    public function logout()
    {
        // Hancurkan hanya sesi admin
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_role']);

        // Redirect ke halaman login admin
        header('Location: /admin/login');
        exit();
    }

    /**
     * Menentukan redirect URL berdasarkan role admin
     */
    private function getRedirectUrlByRole($role)
    {
        switch ($role) {
            case 'superadmin':
                return '/admin/dashboard';
            case 'admin':
                return '/admin/reports';
            case 'operator':
                return '/admin/transactions';
            default:
                return '/admin';
        }
    }

    /**
     * Menampilkan error dalam format yang konsisten
     */
    private function showError($message)
    {
        $_SESSION['admin_login_error'] = $message;
        header('Location: /admin/login');
        exit();
    }

    /**
     * Render view login admin dengan layout khusus
     */
    private function renderAdminLoginView()
    {
        // Gunakan layout khusus admin untuk login
        $error = $_SESSION['admin_login_error'] ?? null;
        unset($_SESSION['admin_login_error']);

        require_once APP_PATH . 'views/layout/header.php';
        require_once APP_PATH . 'views/admin/login.php';
        require_once APP_PATH . 'views/layout/footer.php';
    }
}