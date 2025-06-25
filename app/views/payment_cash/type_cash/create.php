<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-plus-circle text-primary me-2"></i> Tambah Kategori Kas
            </h5>

            <form method="POST" action="/cash/categories/create">
                <!-- Nama Kategori -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama Kategori</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nama kategori" required>
                </div>

                <!-- Deskripsi -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Deskripsi kategori"></textarea>
                </div>

                <!-- Tombol aksi -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="/cash/categories" class="btn btn-outline-secondary">
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