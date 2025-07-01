<?php

/**
 * File: app/views/layout/app.php
 * Deskripsi: Layout utama aplikasi (sidebar, breadcrumb, konten) dengan Bootstrap murni.
 * Variabel: $pageTitle, $breadcrumbs, $contentView
 */

require_once APP_PATH . 'views/layout/header.php';

// Navbar hanya untuk non-login user
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    require_once APP_PATH . 'views/layout/navbar.php';
}
?>

<?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
    <!-- NAVBAR MINI (hanya tampil di mobile) -->
    <nav class="navbar navbar-light bg-white border-bottom sticky-top d-lg-none">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary me-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-semibold fs-5 text-dark">
                    <?= isset($_SESSION['admin_id']) ? 'Admin Panel' : 'Pantaukas' ?>
                </span>
            </div>
        </div>
    </nav>
<?php endif; ?>

<div class="container-fluid">
    <div class="row">
        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
            <!-- SIDEBAR DESKTOP -->
            <aside class="col-auto px-0">
                <?php require_once APP_PATH . 'views/layout/sidebar.php'; ?>
            </aside>
        <?php endif; ?>

        <!-- KONTEN UTAMA -->
        <div class="col d-flex flex-column min-vh-100 px-0">
            <main class="flex-grow-1 p-4">
                <?php if (isset($_SESSION['flash'])): ?>
                    <?php
                    [$type, $message] = $_SESSION['flash'];
                    unset($_SESSION['flash']);

                    // Tentukan icon berdasarkan tipe
                    $icons = [
                        'success' => 'bi-check-circle-fill',
                        'danger'  => 'bi-exclamation-triangle-fill',
                        'warning' => 'bi-exclamation-circle-fill',
                        'info'    => 'bi-info-circle-fill'
                    ];
                    $icon = $icons[$type] ?? 'bi-info-circle-fill';
                    ?>
                    <div id="flashToast" class="toast align-items-center text-white bg-<?= $type ?> border-0 position-fixed bottom-0 end-0 m-4 z-3 show" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi <?= $icon ?> me-2"></i><?= htmlspecialchars($message) ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php
                if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
                    require_once APP_PATH . 'views/layout/breadcrumb.php';
                }
                ?>
                <div class="p-4">
                    <?php
                    if (isset($contentView) && file_exists(APP_PATH . 'views/' . $contentView)) {
                        require_once APP_PATH . 'views/' . $contentView;
                    } else {
                        echo "<div class='alert alert-warning'>Konten halaman tidak ditemukan.</div>";
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>
</div>

<?php
//FOOTER hanya jika TIDAK login
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    require_once APP_PATH . 'views/layout/footer.php';
}
?>