<?php

/**
 * File: app/views/payment_cash/type_cash/index.php
 * Tabel + tombol Create + Pencarian (dengan link Edit berfungsi).
 */
?>

<div class="container py-4">
    <!-- Header + tombol create -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h5 class="fw-semibold mb-0"><i class="bi bi-table text-primary me-2"></i> Data Kategori Transaksi</h5>
        <a href="/cash/categories/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <!-- Search -->
            <div class="p-3 border-bottom">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama kategori...">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th style="width:60px">#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center" style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($type_cash as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['description'] ?? '-') ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($row['id_user'] !== null): ?>
                                            <a href="/cash/categories/<?= $row['id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
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
                            <?php endforeach; ?>
                            <?php if (empty($type_cash)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data.</td>
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
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="hapusNama"></strong>?</p>
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
            document.getElementById('formHapus').action = `/cash/categories/${id}/delete`;
        });
    });
</script>