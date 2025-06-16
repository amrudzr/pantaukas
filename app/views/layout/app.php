<?php

/**
 * File: app/views/layout/app.php
 * Deskripsi: Layout utama aplikasi (sidebar, breadcrumb, konten) dengan Bootstrap murni.
 * Variabel: $pageTitle, $breadcrumbs, $contentView
 */

require_once APP_PATH . 'views/layout/header.php';

// Navbar hanya untuk non-login user
if (!isset($_SESSION['user_id'])) {
    require_once APP_PATH . 'views/layout/navbar.php';
}
?>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- NAVBAR MINI (hanya tampil di mobile) -->
    <nav class="navbar navbar-light bg-white border-bottom sticky-top d-lg-none">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary me-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-semibold fs-5 text-dark">Pantaukas</span>
            </div>
        </div>
    </nav>
<?php endif; ?>

<div class="container-fluid">
    <div class="row">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- SIDEBAR DESKTOP -->
            <aside class="col-auto px-0">
                <?php require_once APP_PATH . 'views/layout/sidebar.php'; ?>
            </aside>
        <?php endif; ?>

        <!-- KONTEN UTAMA -->
        <div class="col d-flex flex-column min-vh-100 px-0">
            <main class="flex-grow-1 p-4">
                <?php
                if (isset($_SESSION['user_id'])) {
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
if (!isset($_SESSION['user_id'])){
    require_once APP_PATH . 'views/layout/footer.php';
}
?>