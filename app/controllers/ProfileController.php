<?php
require_once APP_PATH . 'models/UserModel.php';

class ProfileController
{
    private $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    /** GET /account/profile */
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            redirect('/login');
        }

        $user = $this->model->find($userId);
        if (!$user) {
            $_SESSION['flash'] = ['danger', 'User tidak ditemukan'];
            redirect('/dashboard');
        }

        view('profile/index', [
            'user' => $user,
            'pageTitle' => 'Akun Saya',
            'breadcrumbs' => [
                ['label' => 'Akun', 'url' => 'account/profile']
            ],
        ]);
    }

    /** POST /account/profile/update */
    public function update()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            redirect('/login');
        }

        try {
            $currentUser = $this->model->find($userId);
            $currentPassword = $_POST['current_password'] ?? '';

            // Verifikasi password saat ini
            if (!password_verify($currentPassword, $currentUser['password'])) {
                throw new Exception('Password saat ini salah');
            }

            $data = [
                'name' => trim($_POST['name']),
                'phone' => trim($_POST['phone']),
                'password' => $currentUser['password'] // Default ke password lama
            ];

            // Jika ada password baru
            if (!empty($_POST['password'])) {
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception('Konfirmasi password tidak cocok');
                }
                if (strlen($_POST['password']) < 6) {
                    throw new Exception('Password minimal 6 karakter');
                }
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $this->model->update($userId, $data);

            // Update session
            $_SESSION['name'] = $data['name'];
            $_SESSION['phone'] = $data['phone'];

            $_SESSION['flash'] = ['success', 'Profil berhasil diperbarui'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['danger', $e->getMessage()];
        }

        redirect('/account/profile');
    }

    /** POST /account/profile/delete */
    public function delete()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            redirect('/login');
        }

        try {
            $user = $this->model->find($userId);
            $password = $_POST['password'] ?? '';

            if (!password_verify($password, $user['password'])) {
                throw new Exception('Password salah, penghapusan akun dibatalkan');
            }

            if (!$this->model->delete($userId)) {
                throw new Exception('Gagal menghapus akun');
            }

            session_destroy();
            $_SESSION['flash'] = ['success', 'Akun Anda telah berhasil dihapus'];
            redirect('/login');
        } catch (Exception $e) {
            $_SESSION['flash'] = ['danger', $e->getMessage()];
            redirect('/account/profile');
        }
    }
}
