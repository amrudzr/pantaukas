<?php

/**
 * File: app/views/member/index.php
 * Tabel + tombol Create + Pencarian (dengan link Edit berfungsi).
 */
?>

<div class="container py-4">
    <!-- Header + tombol create -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h5 class="fw-semibold mb-0"><i class="bi bi-table text-primary me-2"></i> Data Anggota</h5>
        <a href="/members/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <!-- Dropdown Import/Export -->
            <div class="p-3 pb-0 dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-upload me-1"></i> Import / Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/members/template">
                            <i class="bi bi-download me-1 text-primary"></i> Download Template
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/members/export">
                            <i class="bi bi-box-arrow-up me-1 text-success"></i> Export Data
                        </a>
                    </li>
                    <li>
                        <form action="/members/import" method="POST" enctype="multipart/form-data" class="px-3 py-2">
                            <label class="form-label small mb-1">Import File</label>
                            <input type="file" name="excel" accept=".xlsx,.xls" class="form-control form-control-sm mb-2" required>
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-upload me-1"></i> Import
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            <!-- Search -->
            <div class="p-3 border-bottom">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau alamat…">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th style="width:60px">#</th>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Status</th>
                            <th class="text-center" style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $statusEn = strtolower($row['status'] ?? '');
                                    $cls = match ($statusEn) {
                                        'active'   => 'bg-primary-subtle text-primary',
                                        'passive'  => 'bg-warning-subtle text-warning',
                                        'inactive' => 'bg-danger-subtle text-danger',
                                        default    => 'bg-secondary'
                                    };

                                    $statusId = match ($statusEn) {
                                        'active'   => 'Aktif',
                                        'passive'  => 'Pasif',
                                        'inactive' => 'Keluar',
                                        default    => 'Tidak diketahui'
                                    };
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= $statusId ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/members/<?= $row['id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-delete" data-bs-toggle="modal" data-bs-target="#modalHapus" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['name']) ?>" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                            <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data.</td>
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
                <p>Apakah Anda yakin ingin menghapus data <strong id="hapusNama"></strong>?</p>
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
    // Script pencarian (sudah ada)
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
            const rowText = tr.textContent.toLowerCase();
            tr.style.display = rowText.includes(query) ? '' : 'none';
        });
    });

    // Script untuk isi modal hapus
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('hapusId').value = id;
            document.getElementById('hapusNama').textContent = name;

            // Optional: atur action dari form jika pakai endpoint dinamis
            document.getElementById('formHapus').action = `/members/${id}/delete`;
        });
    });
</script>