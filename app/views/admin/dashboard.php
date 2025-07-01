<?php
$selectedYear = $_GET['tahun'] ?? date('Y');
?>

<div class="container py-4">
    <!-- Header + Filter Tahun -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">Dashboard Admin</h4>
        <form method="get" class="d-flex gap-2 align-items-center">
            <select name="tahun" class="form-select form-select-sm">
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                    $selected = $selectedYear == $y ? 'selected' : '';
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>
            <button class="btn btn-sm btn-primary" type="submit">
                Filter
            </button>
        </form>
    </div>

    <!-- Card Statistik -->
    <div class="row g-4 mb-4">
        <!-- Total User -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0"><i class="bi bi-people-fill text-primary me-2"></i> Total Pengguna</h5>
                        <span class="badge bg-primary"><?= number_format($totalUsers, 0) ?></span>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="p-2 border rounded text-center">
                                <small class="text-muted">Aktif</small>
                                <h5 class="mb-0 text-success"><?= number_format($activeUsers, 0) ?></h5>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded text-center">
                                <small class="text-muted">Diblokir</small>
                                <h5 class="mb-0 text-warning"><?= number_format($blockedUsers, 0) ?></h5>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded text-center">
                                <small class="text-muted">Dihapus</small>
                                <h5 class="mb-0 text-danger"><?= number_format($deletedUsers, 0) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0"><i class="bi bi-person-badge-fill text-primary me-2"></i> Total Staff</h5>
                        <span class="badge bg-primary"><?= number_format($totalStaff, 0) ?></span>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 border rounded text-center">
                                <small class="text-muted">Aktif</small>
                                <h5 class="mb-0 text-success"><?= number_format($activeStaff, 0) ?></h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded text-center">
                                <small class="text-muted">Nonaktif</small>
                                <h5 class="mb-0 text-secondary"><?= number_format($inactiveStaff, 0) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Perbandingan Bulan Ini vs Bulan Lalu -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-bar-chart-line text-primary me-2"></i> Pertumbuhan Pengguna</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-center">Bulan Ini vs Bulan Lalu</h6>
                    <canvas id="monthlyComparisonChart" height="250"></canvas>
                </div>
                <div class="col-md-6">
                    <h6 class="text-center">Statistik Tahunan (<?= $selectedYear ?>)</h6>
                    <canvas id="yearlyChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-lightning-charge text-primary me-2"></i> Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <a href="/admin/users/create" class="btn btn-outline-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i> Tambah User
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/admin/staff/create" class="btn btn-outline-primary w-100">
                        <i class="bi bi-person-plus me-1"></i> Tambah Staff
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/admin/users?status=blocked" class="btn btn-outline-warning w-100">
                        <i class="bi bi-slash-circle me-1"></i> Lihat User Diblokir
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/admin/reports" class="btn btn-outline-info w-100">
                        <i class="bi bi-file-earmark-text me-1"></i> Generate Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Perbandingan Bulanan
        const monthlyCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Bulan Lalu', 'Bulan Ini'],
                datasets: [{
                    label: 'Jumlah Pengguna',
                    data: [<?= $lastMonthUsers ?>, <?= $currentMonthUsers ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Grafik Tahunan
        const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
        new Chart(yearlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Pengguna Baru',
                    data: <?= json_encode($yearlyUsers) ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>