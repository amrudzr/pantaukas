<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-plus-circle text-primary me-2"></i> Tambah Data Warga
            </h5>

            <form method="post" action="#">
                <!-- Nama -->
                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama lengkap" required>
                </div>

                <!-- Alamat -->
                <div class="mb-3">
                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Alamat lengkap" required></textarea>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="" selected disabled>Pilih status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Pasif">Pasif</option>
                        <option value="Keluar">Keluar</option>
                    </select>
                </div>

                <!-- Tombol aksi -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="/examples" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>