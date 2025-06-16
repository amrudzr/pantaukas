<?php

/**
 * File: app/views/errors/500.php
 * Deskripsi: Tampilan halaman error 500 untuk gagal koneksi database.
 * Tampilan ini digunakan saat terjadi error fatal yang ditangani dari fungsi getDbConnection().
 */
?>

<div class="text-center my-5">
    <h1 class="display-1 fw-bold text-danger">500</h1>
    <h2 class="mb-3">Koneksi ke Database Gagal</h2>
    <p class="text-muted mb-4">
        Sistem tidak dapat terhubung ke database saat ini. Silakan coba beberapa saat lagi atau hubungi administrator.
    </p>

    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-warning text-start mx-auto" style="max-width: 700px;">
            <strong class="d-block mb-2">Detail Error:</strong>
            <pre class="mb-0 text-danger"><?= htmlspecialchars($errorMessage) ?></pre>

            <?php if (!empty($errorDetails)) : ?>
                <hr>
                <small class="d-block text-muted">Info Tambahan:</small>
                <pre class="mb-0 small"><?= htmlspecialchars($errorDetails) ?></pre>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>