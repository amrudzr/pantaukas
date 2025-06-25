<div class="container py-4">
    <!-- Ringkasan Card -->
    <div class="row g-3 mb-4">
        <!-- Card Total -->
        <div class="col-md-4 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Total Iuran</h6>
                        <i class="bi bi-list-check fs-5 text-primary"></i>
                    </div>
                    <h4 class="mb-1"><?= count($type_fees) ?></h4>
                    <small class="text-muted d-block">
                        <?php
                        $counts = [
                            'monthly' => 0,
                            'weekly' => 0,
                            'annually' => 0,
                            'daily' => 0
                        ];

                        foreach ($type_fees as $fee) {
                            $counts[$fee['duration']]++;
                        }

                        $details = [];
                        if ($counts['daily'] > 0) $details[] = $counts['daily'] . ' Harian';
                        if ($counts['weekly'] > 0) $details[] = $counts['weekly'] . ' Mingguan';
                        if ($counts['monthly'] > 0) $details[] = $counts['monthly'] . ' Bulanan';
                        if ($counts['annually'] > 0) $details[] = $counts['annually'] . ' Tahunan';

                        echo implode(', ', $details);
                        ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Card Harian -->
        <div class="col-md-2 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Harian</h6>
                        <i class="bi bi-calendar-day fs-5 text-info"></i>
                    </div>
                    <h4 class="mb-1"><?= $counts['daily'] ?></h4>
                    <small class="text-muted">Iuran harian</small>
                </div>
            </div>
        </div>

        <!-- Card Mingguan -->
        <div class="col-md-2 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Mingguan</h6>
                        <i class="bi bi-calendar-week fs-5 text-warning"></i>
                    </div>
                    <h4 class="mb-1"><?= $counts['weekly'] ?></h4>
                    <small class="text-muted">Iuran mingguan</small>
                </div>
            </div>
        </div>

        <!-- Card Bulanan -->
        <div class="col-md-2 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Bulanan</h6>
                        <i class="bi bi-calendar-month fs-5 text-success"></i>
                    </div>
                    <h4 class="mb-1"><?= $counts['monthly'] ?></h4>
                    <small class="text-muted">Iuran bulanan</small>
                </div>
            </div>
        </div>

        <!-- Card Tahunan -->
        <div class="col-md-2 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Tahunan</h6>
                        <i class="bi bi-calendar-event fs-5 text-danger"></i>
                    </div>
                    <h4 class="mb-1"><?= $counts['annually'] ?></h4>
                    <small class="text-muted">Iuran tahunan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Header + tombol create -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h5 class="fw-semibold mb-0"><i class="bi bi-table text-primary me-2"></i> Data Jenis Iuran</h5>
        <a href="/fee/types/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data
        </a>
    </div>

    <!-- Tabel data -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <!-- Search -->
            <div class="p-3 border-bottom">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama jenis iuran...">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th style="width:60px">#</th>
                            <th>Nama Iuran</th>
                            <th>Nominal</th>
                            <th>Periode</th>
                            <th class="text-center">Detail Iuran</th>
                            <th class="text-center" style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($type_fees as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= 'Rp ' . number_format($row['nominal'], 0, ',', '.') ?></td>
                                <td><?= translateDuration(htmlspecialchars($row['duration'])) ?></td>
                                <td class="text-center">
                                    <a href="/fee/types/<?= $row['id'] ?>/details" class="btn btn-sm btn-outline-success" title="Edit">
                                        <i class="bi bi-folder"></i> Lihat
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($row['id_user'] !== null): ?>
                                            <a href="/fee/types/<?= $row['id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-danger btn-delete" data-bs-toggle="modal" data-bs-target="#modalHapus" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['name']) ?>" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-outline-success" title="Permanen">Permanen</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($type_fees)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger-subtle">
                <h5 class="modal-title" id="modalHapusLabel"><i class="bi bi-exclamation-triangle me-2"></i> Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus jenis iuran <strong id="hapusNama"></strong>?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="formHapus">
                    <input type="hidden" name="id" id="hapusId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
            const rowText = tr.textContent.toLowerCase();
            tr.style.display = rowText.includes(query) ? '' : 'none';
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('hapusId').value = id;
            document.getElementById('hapusNama').textContent = name;
            document.getElementById('formHapus').action = `/fee/types/${id}/delete`;
        });
    });
</script>