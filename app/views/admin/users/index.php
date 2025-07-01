<?php
$selectedStatus = $_GET['status'] ?? 'active';
$searchQuery = $_GET['search'] ?? '';
?>

<div class="container py-4 pt-0">

    <!-- Header + Filter -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <h5 class="fw-semibold mb-0"><i class="bi bi-people-fill text-primary me-2"></i> Data Pengguna</h5>
        <form method="get" class="d-flex gap-2 align-items-center">
            <div class="input-group input-group-sm" style="width: 300px;">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari nama/telepon..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>

            <select name="status" class="form-select form-select-sm" style="width: 150px;">
                <option value="all" <?= $selectedStatus === 'all' ? 'selected' : '' ?>>Semua Status</option>
                <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Aktif</option>
                <option value="blocked" <?= $selectedStatus === 'blocked' ? 'selected' : '' ?>>Diblokir</option>
                <option value="deleted" <?= $selectedStatus === 'deleted' ? 'selected' : '' ?>>Dihapus</option>
            </select>

            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" type="submit">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Total Pengguna</h6>
                        <i class="bi bi-people fs-5 text-primary"></i>
                    </div>
                    <h4 class="d-flex justify-content-between align-items-center">
                        <span id="totalUsers"><?= number_format($totalUsers, 0, ',', '.') ?></span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Pengguna Aktif</h6>
                        <i class="bi bi-check-circle fs-5 text-success"></i>
                    </div>
                    <h4 class="d-flex justify-content-between align-items-center">
                        <span id="activeUsers"><?= number_format($activeUsers, 0, ',', '.') ?></span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Pengguna Diblokir</h6>
                        <i class="bi bi-slash-circle fs-5 text-warning"></i>
                    </div>
                    <h4 class="d-flex justify-content-between align-items-center">
                        <span id="blockedUsers"><?= number_format($blockedUsers, 0, ',', '.') ?></span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Pengguna Dihapus</h6>
                        <i class="bi bi-trash fs-5 text-danger"></i>
                    </div>
                    <h4 class="d-flex justify-content-between align-items-center">
                        <span id="deletedUsers"><?= number_format($deletedUsers, 0, ',', '.') ?></span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end mb-3">
        <a href="/admin/users/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
        </a>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Terakhir Login</th>
                            <th class="text-center" style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $i => $user): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td>
                                        <?php if (in_array($user['status'], ['blocked', 'deleted']) && !empty($user['id_admin'])): ?>
                                            <span class="badge <?= $user['status'] === 'blocked' ? 'bg-warning text-dark' : 'bg-danger' ?>"
                                                data-bs-toggle="popover"
                                                data-bs-container="body"
                                                data-bs-placement="top"
                                                data-bs-trigger="hover focus"
                                                data-bs-title="Informasi Status"
                                                data-bs-content="Akun ini di<?= $user['status'] === 'blocked' ? 'blokir' : 'hapus' ?> oleh admin: <?= htmlspecialchars($user['admin_name'] ?? 'Admin tidak diketahui') ?>">
                                                <?= $user['status'] === 'blocked' ? 'Diblokir' : 'Dihapus' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : '' ?>">
                                                <?= $user['status'] === 'active' ? 'Aktif' : '' ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Belum pernah' ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <?php if ($user['status'] === 'active'): ?>
                                                <button type="button" class="btn btn-outline-warning" title="Blokir"
                                                    data-bs-toggle="modal" data-bs-target="#blockModal"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    data-user-name="<?= htmlspecialchars($user['name']) ?>">
                                                    <i class="bi bi-slash-circle"></i>
                                                </button>
                                            <?php elseif ($user['status'] === 'blocked'): ?>
                                                <button type="button" class="btn btn-outline-success" title="Aktifkan"
                                                    data-bs-toggle="modal" data-bs-target="#activateModal"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    data-user-name="<?= htmlspecialchars($user['name']) ?>">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            <?php endif; ?>

                                            <button type="button" class="btn btn-outline-danger" title="Hapus"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-user-id="<?= $user['id'] ?>"
                                                data-user-name="<?= htmlspecialchars($user['name']) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada data pengguna.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Blokir -->
<div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title" id="blockModalLabel"><i class="bi bi-slash-circle me-2"></i> Konfirmasi Blokir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memblokir pengguna <strong id="blockUserName"></strong>?</p>
                <p class="text-warning small">Pengguna yang diblokir tidak dapat login sampai diaktifkan kembali.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="blockForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Blokir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Aktifkan -->
<div class="modal fade" id="activateModal" tabindex="-1" aria-labelledby="activateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success-subtle">
                <h5 class="modal-title" id="activateModalLabel"><i class="bi bi-check-circle me-2"></i> Konfirmasi Aktivasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengaktifkan kembali pengguna <strong id="activateUserName"></strong>?</p>
                <p class="text-success small">Pengguna akan dapat login kembali setelah diaktifkan.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="activateForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger-subtle">
                <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-trash me-2"></i> Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengguna <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger small">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi popover
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Fungsi pencarian
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        });

        // Handle modal blokir
        const blockModal = document.getElementById('blockModal');
        blockModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            blockModal.querySelector('#blockUserName').textContent = userName;
            blockModal.querySelector('#blockForm').action = `/admin/users/${userId}/block`;
        });

        // Handle modal aktifkan
        const activateModal = document.getElementById('activateModal');
        activateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            activateModal.querySelector('#activateUserName').textContent = userName;
            activateModal.querySelector('#activateForm').action = `/admin/users/${userId}/activate`;
        });

        // Handle modal hapus
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            deleteModal.querySelector('#deleteUserName').textContent = userName;
            deleteModal.querySelector('#deleteForm').action = `/admin/users/${userId}/delete`;
        });
    });
</script>