<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-plus-circle text-primary me-2"></i> Tambah Data Anggota
            </h5>

            <form method="POST" action="/members/create">
                <!-- Nama -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nama lengkap" required>
                </div>

                <!-- Nomor Telepon -->
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="08XXXXXXXX">
                </div>

                <!-- Alamat -->
                <div class="mb-3">
                    <label for="address" class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Alamat anggota"></textarea>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active">Aktif</option>
                        <option value="passive">Pasif</option>
                        <option value="inactive">Keluar</option>
                    </select>
                </div>

                <!-- Tombol aksi -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="/members" class="btn btn-outline-secondary">
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