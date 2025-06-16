<!-- Kontainer utama untuk memposisikan form login di tengah halaman -->
<div class="container d-flex justify-content-center align-items-center py-5" style="min-height: calc(100vh - 100px);">
    <div class="card p-4 shadow-lg w-100" style="max-width: 400px;">
        <!-- Teks brand -->
        <div class="text-center mb-2">
            <a href="/" class="text-decoration-none fw-bold fs-4 text-primary">Pantaukas</a>
        </div>
        <h2 class="card-title text-center mb-4">Login</h2>
        <form action="/login" method="POST">
            <div class="mb-3">
                <label for="phone" class="form-label">Nomor Telepon:</label>
                <input type="text" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            <!-- Password input with toggle -->
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
            <p class="text-center">Belum punya akun? <a href="/register">Daftar di sini</a>.</p>
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