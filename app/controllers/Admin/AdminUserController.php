<?php
require_once APP_PATH . 'models/UserModel.php';

class AdminUserController
{
    private $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    /** GET /admin/users */
    public function index()
    {
        // Generate CSRF token jika belum ada
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? 'active';

        // Get all users with counts
        $users = $this->getAllUsers($search, $status);

        // Get counts for each status
        $counts = $this->getUserCounts();

        view('admin/users/index', [
            'users' => $users,
            'totalUsers' => $counts['total'],
            'activeUsers' => $counts['active'],
            'blockedUsers' => $counts['blocked'],
            'deletedUsers' => $counts['deleted'],
            'pageTitle' => 'Data Pengguna',
            'breadcrumbs' => [
                ['label' => 'Pengguna', 'url' => '/admin/users']
            ],
            'status' => $status,
            'search' => $search,
            'csrf_token' => $_SESSION['csrf_token'] // Kirim token ke view
        ]);
    }

    /** ANY /admin/users/create */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

                $userId = $this->model->createUser(
                    trim($_POST['name']),
                    '0' . trim($_POST['phone']),
                    $hashedPassword
                );

                if ($userId) {
                    $_SESSION['flash'] = ['success', 'Pengguna berhasil ditambahkan.'];
                    redirect('/admin/users');
                } else {
                    $_SESSION['flash'] = ['danger', 'Gagal menambahkan pengguna.'];
                }
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('admin/users/create', [
            'pageTitle' => 'Tambah Pengguna',
            'breadcrumbs' => [
                ['label' => 'Pengguna', 'url' => '/admin/users'],
                ['label' => 'Tambah', 'url' => '/admin/users/create']
            ],
        ]);
    }

    /** ANY /admin/users/{id}/edit */
    public function edit($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            showErrorPage(404);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => trim($_POST['name']),
                    'phone' => '0' . trim($_POST['phone']),
                    'password' => $user['password'] // Default to existing password
                ];

                // Only update password if provided
                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $this->model->update($id, $data);
                $_SESSION['flash'] = ['success', 'Data pengguna berhasil diperbarui.'];
                redirect('/admin/users');
            } catch (Exception $e) {
                $_SESSION['flash'] = ['danger', $e->getMessage()];
            }
        }

        view('admin/users/edit', [
            'user' => $user,
            'pageTitle' => 'Edit Pengguna',
            'breadcrumbs' => [
                ['label' => 'Pengguna', 'url' => '/admin/users'],
                ['label' => 'Edit', 'url' => '/admin/users/' . $id . '/edit']
            ],
        ]);
    }

    /** POST /admin/users/{id}/block */
    public function block($id)
    {
        $conn = getDbConnection();
        $adminId = $_SESSION['admin_id'] ?? null; // Asumsikan admin_id disimpan di session

        $stmt = $conn->prepare("UPDATE user SET status = 'blocked', id_admin = ? WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $id);

        if ($stmt->execute()) {
            $_SESSION['flash'] = ['success', 'Pengguna berhasil diblokir.'];
        } else {
            $_SESSION['flash'] = ['danger', 'Gagal memblokir pengguna.'];
        }

        redirect('/admin/users');
    }

    /** POST /admin/users/{id}/activate */
    public function activate($id)
    {
        $conn = getDbConnection();

        // Validasi CSRF token jika diperlukan
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['danger', 'Token CSRF tidak valid'];
            redirect('/admin/users');
            return;
        }

        // Set status ke active dan kosongkan id_admin (karena diaktifkan oleh sistem)
        $stmt = $conn->prepare("UPDATE user SET status = 'active', id_admin = NULL WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['flash'] = ['success', 'Pengguna berhasil diaktifkan kembali.'];
        } else {
            $_SESSION['flash'] = ['danger', 'Gagal mengaktifkan pengguna: ' . $stmt->error];
        }

        redirect('/admin/users');
    }

    /** POST /admin/users/{id}/delete */
    public function delete($id)
    {
        $conn = getDbConnection();
        $adminId = $_SESSION['admin_id'] ?? null; // Asumsikan admin_id disimpan di session

        $stmt = $conn->prepare("UPDATE user SET status = 'deleted', deleted_at = CURRENT_TIMESTAMP, id_admin = ? WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $id);

        if ($stmt->execute()) {
            $_SESSION['flash'] = ['success', 'Pengguna berhasil dihapus.'];
        } else {
            $_SESSION['flash'] = ['danger', 'Gagal menghapus pengguna.'];
        }

        redirect('/admin/users');
    }

    private function getUserCounts()
    {
        $conn = getDbConnection();

        // Get total count
        $total = $conn->query("SELECT COUNT(*) FROM user")->fetch_row()[0];

        // Get active count
        $active = $conn->query("SELECT COUNT(*) FROM user WHERE status = 'active'")->fetch_row()[0];

        // Get blocked count
        $blocked = $conn->query("SELECT COUNT(*) FROM user WHERE status = 'blocked'")->fetch_row()[0];

        // Get deleted count
        $deleted = $conn->query("SELECT COUNT(*) FROM user WHERE status = 'deleted'")->fetch_row()[0];

        return [
            'total' => $total,
            'active' => $active,
            'blocked' => $blocked,
            'deleted' => $deleted
        ];
    }

    /**
     * Helper method to get all users with optional search and status filter
     */
    private function getAllUsers($search = '', $status = 'active')
    {
        $conn = getDbConnection();
        $query = "SELECT u.*, a.name as admin_name FROM user u 
          LEFT JOIN admin a ON u.id_admin = a.id 
          WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($search)) {
            $query .= " AND (u.name LIKE ? OR u.phone LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }

        if (!empty($status) && $status !== 'all') {
            $query .= " AND u.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        $query .= " ORDER BY u.created_at DESC";

        $stmt = $conn->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $users;
    }

    /**
     * Helper method to update user status
     */
    private function updateStatus($id, $status)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE user SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
