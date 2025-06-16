<?php
// Dummy data untuk edit (ganti dengan data dari controller/mode)
$data = $data ?? [
    'id' => 1,
    'nama' => 'Ahmad Surya',
    'alamat' => 'Jl. Melati No. 15',
    'status' => 'Aktif'
];
?>

<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Warga
            </h5>

            <form method="post" action="/examples/update/<?= $data['id'] ?>">
                <!-- Nama -->
                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" required
                        value="<?= htmlspecialchars($data['nama']) ?>">
                </div>

                <!-- Alamat -->
                <div class="mb-3">
                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" required><?= htmlspecialchars($data['alamat']) ?></textarea>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="" disabled>Pilih status</option>
                        <option value="Aktif" <?= $data['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Pasif" <?= $data['status'] === 'Pasif' ? 'selected' : '' ?>>Pasif</option>
                        <option value="Keluar" <?= $data['status'] === 'Keluar' ? 'selected' : '' ?>>Keluar</option>
                    </select>
                </div>

                <!-- Tombol aksi -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="/examples" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="bi bi-save2 me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>