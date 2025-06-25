<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-pencil-square text-primary me-2"></i> Edit Jenis Iuran
            </h5>

            <form method="POST" action="/fee/types/<?= $data['id'] ?>/edit">
                <!-- Nama Iuran -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama Iuran</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($data['name']) ?>" placeholder="Nama jenis iuran" required>
                </div>

                <!-- Baris sejajar untuk Nominal dan Periode -->
                <div class="row mb-3">
                    <!-- Nominal (Kolom kiri) -->
                    <div class="col-md-6">
                        <label for="nominalDisplay" class="form-label fw-semibold">Nominal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="nominalDisplay" placeholder="100.000"
                                value="<?= number_format($data['nominal'], 0, ',', '.') ?>" autocomplete="off">
                            <input type="hidden" name="nominal" id="nominalRaw" value="<?= $data['nominal'] ?>">
                        </div>
                    </div>

                    <!-- Periode (Kolom kanan) -->
                    <div class="col-md-6">
                        <label for="duration" class="form-label fw-semibold">Periode Pembayaran</label>
                        <select class="form-select" id="duration" name="duration" required>
                            <option value="daily" <?= $data['duration'] === 'daily' ? 'selected' : '' ?>>Harian</option>
                            <option value="weekly" <?= $data['duration'] === 'weekly' ? 'selected' : '' ?>>Mingguan</option>
                            <option value="monthly" <?= $data['duration'] === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="annually" <?= $data['duration'] === 'annually' ? 'selected' : '' ?>>Tahunan</option>
                        </select>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Deskripsi jenis iuran"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                </div>

                <!-- Tombol aksi -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="/fee/types" class="btn btn-outline-secondary">
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

<script>
    const nominalDisplay = document.getElementById('nominalDisplay');
    const nominalRaw = document.getElementById('nominalRaw');

    nominalDisplay.addEventListener('input', function() {
        // Hapus semua karakter non-digit
        let value = this.value.replace(/\D/g, '');

        // Simpan nilai raw (tanpa format) ke input hidden
        nominalRaw.value = value;

        // Format dengan titik sebagai separator ribuan
        this.value = formatRupiah(value);
    });

    // Fungsi untuk memformat angka ke format Rupiah
    function formatRupiah(angka) {
        if (!angka) return '';

        // Balik string untuk memudahkan penambahan separator
        let reversed = angka.toString().split('').reverse().join('');
        let ribuan = reversed.match(/\d{1,3}/g);

        // Gabungkan dengan titik sebagai separator dan balik kembali
        return ribuan.join('.').split('').reverse().join('');
    }

    // Format nilai saat pertama kali load
    document.addEventListener('DOMContentLoaded', function() {
        if (nominalRaw.value) {
            nominalDisplay.value = formatRupiah(nominalRaw.value);
        }
    });
</script>