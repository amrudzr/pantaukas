<?php
require_once APP_PATH . 'models/UserModel.php';
require_once APP_PATH . 'models/Admin/AdminModel.php';

class AdminDashboardController
{
    private $userModel;
    private $staffModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->staffModel = new AdminModel();
    }

    /** GET /admin/dashboard */
    public function index()
    {
        $selectedYear = $_GET['tahun'] ?? date('Y');

        // Data User
        $totalUsers = $this->userModel->getCount();
        $activeUsers = $this->userModel->getCountByStatus('active');
        $blockedUsers = $this->userModel->getCountByStatus('blocked');
        $deletedUsers = $this->userModel->getCountByStatus('deleted');

        // Data Staff
        $totalStaff = $this->staffModel->getCount();
        $activeStaff = $this->staffModel->getCountByStatus('active');
        $inactiveStaff = $this->staffModel->getCountByStatus('inactive');

        // Data Grafik User
        $currentMonthUsers = $this->userModel->getMonthlyCount(date('m'), date('Y'));
        $lastMonthUsers = $this->userModel->getMonthlyCount(date('m', strtotime('-1 month')), date('Y', strtotime('-1 month')));
        $yearlyUsers = $this->userModel->getYearlyCount($selectedYear);

        view('admin/dashboard', [
            'pageTitle' => 'Dashboard Admin',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/admin/dashboard']
            ],
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'blockedUsers' => $blockedUsers,
            'deletedUsers' => $deletedUsers,
            'totalStaff' => $totalStaff,
            'activeStaff' => $activeStaff,
            'inactiveStaff' => $inactiveStaff,
            'currentMonthUsers' => $currentMonthUsers,
            'lastMonthUsers' => $lastMonthUsers,
            'yearlyUsers' => $yearlyUsers,
            'selectedYear' => $selectedYear
        ]);
    }
}
