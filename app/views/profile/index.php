<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-person-circle text-primary me-2"></i> Akun Saya
            </h5>

            <!-- Informasi Profil -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($_SESSION['name'] ?? '') ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($_SESSION['phone'] ?? '') ?></small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="profileActions" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear"></i> Aksi
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="profileActions">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateProfileModal"><i class="bi bi-pencil me-2"></i>Update Profil</a></li>
                            <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteAccountModal"><i class="bi bi-trash me-2"></i>Hapus Akun</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Detail Profil -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Informasi Akun</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-calendar me-2"></i> Bergabung sejak: <?= date('d M Y', strtotime($user['created_at'] ?? '')) ?></li>
                                <li class="mb-2"><i class="bi bi-clock-history me-2"></i> Login terakhir: <?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Belum pernah' ?></li>
                                <li><i class="bi bi-circle-fill me-2 text-<?= ($user['status'] ?? '') === 'active' ? 'success' : 'danger' ?>"></i> Status: <?= ucfirst($user['status'] ?? '') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Profil -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/account/profile/update">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProfileModalLabel"><i class="bi bi-pencil-square me-2"></i>Update Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nama -->
                    <div class="mb-3">
                        <label for="updateName" class="form-label fw-semibold">Nama</label>
                        <input type="text" class="form-control" id="updateName" name="name" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" required>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="mb-3">
                        <label for="updatePhone" class="form-label fw-semibold">Nomor Telepon</label>
                        <input type="text" class="form-control" id="updatePhone" name="phone" value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" required>
                    </div>

                    <!-- Password (opsional) -->
                    <div class="mb-3">
                        <label for="updatePassword" class="form-label fw-semibold">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="updatePassword" name="password">
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-3">
                        <label for="updateConfirmPassword" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="updateConfirmPassword" name="confirm_password">
                    </div>

                    <!-- Password Saat Ini (wajib untuk update) -->
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label fw-semibold">Password Saat Ini*</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        <small class="text-muted">*Diperlukan untuk verifikasi perubahan</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Akun -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="deleteStep1">
                    <p>Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.</p>
                    <p class="fw-semibold">Semua data terkait akun ini akan dihapus secara permanen.</p>
                </div>

                <div id="deleteStep2" style="display: none;">
                    <form id="deleteAccountForm" method="POST" action="/account/profile/delete">
                        <div class="mb-3">
                            <label for="deletePassword" class="form-label fw-semibold">Masukkan Password Anda</label>
                            <input type="password" class="form-control" id="deletePassword" name="password" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                            <label class="form-check-label" for="confirmDelete">
                                Saya mengerti bahwa semua data akan dihapus permanen
                            </label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="deleteNextBtn" class="btn btn-danger">Lanjutkan</button>
                <button type="submit" form="deleteAccountForm" id="deleteConfirmBtn" class="btn btn-danger" style="display: none;">Konfirmasi Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Script untuk modal hapus akun
    document.addEventListener('DOMContentLoaded', function() {
        const deleteNextBtn = document.getElementById('deleteNextBtn');
        const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
        const deleteStep1 = document.getElementById('deleteStep1');
        const deleteStep2 = document.getElementById('deleteStep2');

        deleteNextBtn.addEventListener('click', function() {
            deleteStep1.style.display = 'none';
            deleteStep2.style.display = 'block';
            deleteNextBtn.style.display = 'none';
            deleteConfirmBtn.style.display = 'block';
        });
    });
</script>