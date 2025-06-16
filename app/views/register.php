<!-- Kontainer utama untuk memposisikan form registrasi di tengah halaman -->
<div class="container d-flex justify-content-center align-items-center py-5" style="min-height: calc(100vh - 100px);">
    <div class="card p-4 shadow-lg w-100" style="max-width: 400px;">
        <!-- Teks brand -->
        <div class="text-center mb-2">
            <a href="/" class="text-decoration-none fw-bold fs-4 text-primary">Pantaukas</a>
        </div>
        <h2 class="card-title text-center mb-4">Registrasi</h2>
        <form action="/register" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap:</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Nomor Telepon:</label>
                <input type="text" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            <!-- Password -->
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-3 position-relative">
                <label for="confirm_password" class="form-label">Konfirmasi Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Daftar</button>
            <p class="text-center">Sudah punya akun? <a href="/login">Login di sini</a>.</p>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = btn.querySelector('i');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
</script>