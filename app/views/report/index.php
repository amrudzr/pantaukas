<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">
                <i class="bi bi-folder-fill me-2 text-primary"></i> Unduh & Bagikan Laporan
            </h5>

            <form class="row gy-3 gx-3">
                <!-- Jenis Laporan -->
                <div class="col-md-4">
                    <label for="jenis" class="form-label text-muted small">Jenis Laporan</label>
                    <select id="jenis" name="jenis" class="form-select shadow-sm" required>
                        <option value="" disabled selected>Pilih jenis laporan</option>
                        <option value="iuran">Iuran</option>
                        <option value="kas">Kas</option>
                    </select>
                </div>

                <!-- Periode Bulan -->
                <div class="col-md-4">
                    <label for="bulan" class="form-label text-muted small">Periode Bulan</label>
                    <select id="bulan" name="bulan" class="form-select shadow-sm" required>
                        <?php
                        $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        foreach ($bulanList as $i => $nama): ?>
                            <option value="<?= $i + 1 ?>"><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tahun -->
                <div class="col-md-4">
                    <label for="tahun" class="form-label text-muted small">Tahun</label>
                    <select id="tahun" name="tahun" class="form-select shadow-sm" required>
                        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Tombol -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-download me-1"></i> Download
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="bi bi-share-fill me-1"></i> Bagikan Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bagikan Link -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm rounded-3">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="shareModalLabel">Bagikan Laporan via URL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <label for="shareUrl" class="form-label text-muted small">URL Akses:</label>
                <div class="input-group shadow-sm">
                    <input type="text" id="shareUrl" class="form-control" readonly value="">
                    <button class="btn btn-outline-primary" type="button" id="copyBtn">
                        <i class="bi bi-clipboard"></i> Salin
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Salin dan bagikan URL ini agar orang lain dapat mengakses laporan.</small>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('[data-bs-target="#shareModal"]').addEventListener('click', function () {
        const jenis = document.getElementById('jenis').value;
        const bulan = document.getElementById('bulan').value;
        const tahun = document.getElementById('tahun').value;

        const baseUrl = window.location.origin + '/laporan/view';
        const link = `${baseUrl}?jenis=${jenis}&bulan=${bulan}&tahun=${tahun}`;
        document.getElementById('shareUrl').value = link;
    });

    document.getElementById('copyBtn').addEventListener('click', function () {
        const input = document.getElementById('shareUrl');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        this.innerHTML = '<i class="bi bi-check2-circle"></i> Tersalin!';
        setTimeout(() => {
            this.innerHTML = '<i class="bi bi-clipboard"></i> Salin';
        }, 2000);
    });
</script>
