<?php
/**
 * File: app/views/layout/sidebar.php
 * Deskripsi: Komponen sidebar untuk user dan admin dengan menu berbeda
 */
$currentUri = $currentUri ?? '';
$isAdmin = isset($_SESSION['admin_id']);
$isSuperAdmin = $isAdmin && $_SESSION['admin_role'] === 'superadmin';
?>

<!-- ===== DESKTOP SIDEBAR (≥ lg) ===== -->
<aside class="d-none d-lg-flex flex-column flex-shrink-0 p-3 bg-white border-end vh-100" style="width:240px;">
    <!-- Judul -->
    <div class="d-flex align-items-center mb-4">
        <span class="fs-5 fw-semibold text-dark"><?= $isAdmin ? 'Admin Panel' : 'Pantaukas' ?></span>
    </div>

    <!-- Menu -->
    <ul class="nav nav-pills flex-column gap-1 mb-auto">
        <?php if ($isAdmin): ?>
            <!-- Menu Admin -->
            <li class="nav-item">
                <a href="/admin/dashboard"
                   class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/dashboard')===0 ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/users"
                   class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/users')===0 ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-people-fill me-2"></i> Pengguna
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/staff"
                   class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/staff')===0 ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-person-badge me-2"></i> Staff
                </a>
            </li>
            <?php if ($isSuperAdmin): ?>
                <li class="nav-item">
                    <a href="/admin/system"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/system')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-gear-fill me-2"></i> System
                    </a>
                </li>
            <?php endif; ?>
        <?php else: ?>
            <!-- Menu User Biasa -->
            <li class="nav-item">
                <a href="/dashboard"
                   class="nav-link d-flex align-items-center <?= ($currentUri === 'dashboard' || $currentUri === '') ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/members"
                   class="nav-link d-flex align-items-center <?= strpos($currentUri,'members')===0 ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-people-fill me-2"></i> Anggota
                </a>
            </li>
            <li class="nav-item">
                <a href="/fee/types"
                   class="nav-link d-flex align-items-center <?= strpos($currentUri,'fee/types')===0 ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-coin me-2"></i> Iuran
                </a>
            </li>
            <li class="nav-item">
                <a href="/reports"
                   class="nav-link d-flex align-items-center <?= strpos($currentUri,'reports')===0 ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-folder me-2"></i> Laporan
                </a>
            </li>
            <li class="nav-item">
                <a href="/cash"
                   class="nav-link d-flex align-items-center <?= strpos($currentUri,'cash')===0 ? 'active' : 'text-dark'; ?>">
                    <i class="bi bi-wallet me-2"></i> Kas
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <!-- Dropdown Akun -->
    <div class="dropdown mt-3">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
           href="#"
           id="dropdownUser"
           data-bs-toggle="dropdown"
           aria-expanded="false">
            <i class="bi bi-person-circle me-2 fs-5"></i>
            <strong><?= $isAdmin ? ($_SESSION['admin_name'] ?? 'Admin') : 'Account' ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
            <li>
                <a class="dropdown-item" href="<?= $isAdmin ? '/admin/profile' : '/account/profile' ?>">
                    <i class="bi bi-person me-2"></i> Profile
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="<?= $isAdmin ? '/admin/logout' : '/logout' ?>">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- ===== MOBILE OFFCANVAS (< lg) ===== -->
<div class="offcanvas offcanvas-start d-lg-none"
     tabindex="-1"
     id="mobileSidebar"
     aria-labelledby="mobileSidebarLabel"
     style="--bs-offcanvas-width:240px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="mobileSidebarLabel"><?= $isAdmin ? 'Admin Panel' : 'Pantaukas' ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 d-flex flex-column">
        <!-- NAV (copy dari sidebar desktop) -->
        <ul class="nav nav-pills flex-column gap-1 mb-auto">
            <?php if ($isAdmin): ?>
                <!-- Menu Admin Mobile -->
                <li class="nav-item">
                    <a href="/admin/dashboard"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/dashboard')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-grid-fill me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/users"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/users')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-people-fill me-2"></i> Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/staff"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/staff')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-person-badge me-2"></i> Staff
                    </a>
                </li>
                <?php if ($isSuperAdmin): ?>
                    <li class="nav-item">
                        <a href="/admin/system"
                           class="nav-link d-flex align-items-center <?= strpos($currentUri,'admin/system')===0 ? 'active' : 'text-dark'; ?>">
                            <i class="bi bi-gear-fill me-2"></i> System
                        </a>
                    </li>
                <?php endif; ?>
            <?php else: ?>
                <!-- Menu User Biasa Mobile -->
                <li class="nav-item">
                    <a href="/dashboard"
                       class="nav-link d-flex align-items-center <?= ($currentUri === 'dashboard' || $currentUri === '') ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-grid-fill me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/members"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'members')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-people-fill me-2"></i> Anggota
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/fee/types"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'fee/types')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-coin me-2"></i> Iuran
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/reports"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'reports')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-folder me-2"></i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/cash"
                       class="nav-link d-flex align-items-center <?= strpos($currentUri,'cash')===0 ? 'active' : 'text-dark'; ?>">
                        <i class="bi bi-wallet me-2"></i> Kas
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- DROPDOWN AKUN MOBILE -->
        <div class="dropdown">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
               href="#"
               id="dropdownUserMobile"
               data-bs-toggle="dropdown"
               aria-expanded="false">
                <i class="bi bi-person-circle me-2 fs-5"></i>
                <strong><?= $isAdmin ? ($_SESSION['admin_name'] ?? 'Admin') : 'Account' ?></strong>
            </a>
            <ul class="dropdown-menu shadow" aria-labelledby="dropdownUserMobile">
                <li>
                    <a class="dropdown-item" href="<?= $isAdmin ? '/admin/profile' : '/account/profile' ?>">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?= $isAdmin ? '/admin/logout' : '/logout' ?>">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>