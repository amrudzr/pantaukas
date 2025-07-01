<?php date_default_timezone_set('Asia/Jakarta'); ?>
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h5 class="mb-3 fw-semibold">
                <i class="bi bi-person-gear me-1"></i> Edit Data Pengguna
            </h5>

            <form method="POST" action="/admin/users/<?= $user['id'] ?>/edit">
                <div class="row g-3">
                    <!-- Nama -->
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" 
                            value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <!-- Telepon -->
                    <div class="col-md-6">
                        <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">+62</span>
                            <input type="text" class="form-control" name="phone" id="phoneInput"
                                value="<?= htmlspecialchars(ltrim($user['phone'], '0')) ?>" required>
                        </div>
                        <small class="text-muted">Tanpa awalan 0 (contoh: 81234567890)</small>
                    </div>

                    <!-- Password -->
                    <div class="col-md-6">
                        <label class="form-label">Password Baru</label>
                        <div class="input-group input-group-sm">
                            <input type="password" class="form-control" name="password" 
                                id="passwordInput" placeholder="Kosongkan jika tidak diubah">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="input-group input-group-sm">
                            <input type="password" class="form-control" name="confirm_password" 
                                id="confirmPasswordInput" placeholder="Ulangi password baru">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-12">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="status" required>
                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="blocked" <?= $user['status'] === 'blocked' ? 'selected' : '' ?>>Diblokir</option>
                        </select>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="/admin/users" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // Phone number validation - remove all non-digits and leading zeros
    document.getElementById('phoneInput').addEventListener('input', function() {
        // Remove all non-digit characters
        let phoneNumber = this.value.replace(/\D/g, '');
        // Remove leading zeros
        phoneNumber = phoneNumber.replace(/^0+/, '');
        this.value = phoneNumber;
    });

    // Password confirmation check
    document.getElementById('confirmPasswordInput').addEventListener('input', function() {
        const password = document.getElementById('passwordInput').value;
        const confirmPassword = this.value;
        const matchIndicator = document.getElementById('passwordMatch');
        
        if (confirmPassword.length === 0) {
            if (matchIndicator) matchIndicator.textContent = '';
            return;
        }
        
        if (password === confirmPassword) {
            if (matchIndicator) matchIndicator.textContent = 'Password cocok';
            if (matchIndicator) matchIndicator.className = 'text-success';
        } else {
            if (matchIndicator) matchIndicator.textContent = 'Password tidak cocok';
            if (matchIndicator) matchIndicator.className = 'text-danger';
        }
    });

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('passwordInput').value;
        const confirmPassword = document.getElementById('confirmPasswordInput').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return;
        }
        
        if (password && password.length < 8) {
            e.preventDefault();
            alert('Password harus minimal 8 karakter!');
            return;
        }
    });
</script>