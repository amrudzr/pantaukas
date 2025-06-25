<?php
$selectedMonth = $_GET['bulan'] ?? date('m');
$selectedYear = $_GET['tahun'] ?? date('Y');

$saldoKas = $saldoKas ?? 0;
$totalPemasukan = $totalPemasukan ?? 0;
$totalPengeluaran = $totalPengeluaran ?? 0;
?>

<div class="container py-4 pt-0">

    <!-- Ringkasan + Filter Bulan & Tahun -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <h5 class="fw-semibold mb-0"><i class="bi bi-wallet2 text-primary me-2"></i> Kas</h5>
        <form method="get" class="d-flex gap-2 align-items-center">
            <select name="bulan" class="form-select form-select-sm">
                <?php
                $bulanList = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                ];
                foreach ($bulanList as $val => $label) {
                    $selected = $selectedMonth == $val ? 'selected' : '';
                    echo "<option value='$val' $selected>$label</option>";
                }
                ?>
            </select>

            <select name="tahun" class="form-select form-select-sm">
                <?php
                $year = date('Y');
                for ($i = $year; $i >= $year - 5; $i--) {
                    $selected = $selectedYear == $i ? 'selected' : '';
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>

            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" type="submit">
                <i class="bi bi-funnel"></i> Tampilkan
            </button>
        </form>
    </div>

    <!-- Ringkasan Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Saldo Kas</h6>
                        <i class="bi bi-cash-stack fs-5 text-primary"></i>
                    </div>
                    <h4 class="d-flex justify-content-between align-items-center">
                        <span id="saldoKas" data-real="Rp <?= number_format($saldoKas, 0, ',', '.') ?>" data-hidden="false">
                            Rp <?= number_format($saldoKas, 0, ',', '.') ?>
                        </span>
                        <i class="bi bi-eye toggle-eye text-secondary fs-6" role="button" data-target="saldoKas"></i>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Total Pemasukan</h6>
                        <i class="bi bi-arrow-down-circle fs-5 text-success"></i>
                    </div>
                    <h4 class="d-flex justify-content-between align-items-center">
                        <span id="totalIn" data-real="Rp <?= number_format($totalPemasukan, 0, ',', '.') ?>" data-hidden="false">
                            Rp <?= number_format($totalPemasukan, 0, ',', '.') ?>
                        </span>
                        <i class="bi bi-eye toggle-eye text-secondary fs-6" role="button" data-target="totalIn"></i>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Total Pengeluaran</h6>
                        <i class="bi bi-arrow-up-circle fs-5 text-danger"></i>
                    </div>
                    <h4 class="d-flex justify-content-between align-items-center">
                        <span id="totalOut" data-real="Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?>" data-hidden="false">
                            Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?>
                        </span>
                        <i class="bi bi-eye toggle-eye text-secondary fs-6" role="button" data-target="totalOut"></i>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi: Cek Kategori dan Buat Transaksi -->
    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
        <a href="/cash/categories" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-list-task me-1"></i> Cek Kategori
        </a>
        <a href="/cash/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Buat Transaksi
        </a>
    </div>


    <!-- Tabel Transaksi -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <!-- Search -->
            <div class="p-3 border-bottom">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari judul transaksi atau kategori…">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                            <th class="text-center" style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cash)): ?>
                            <?php foreach ($cash as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['payment_date'])) ?></td>
                                    <td><?= $row['type'] === 'out' ? '-' . format_rupiah($row['nominal']) : format_rupiah($row['nominal']) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/cash/<?= $row['id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-danger btn-delete" data-bs-toggle="modal" data-bs-target="#modalHapus" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['title']) ?>" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi kas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger-subtle">
                <h5 class="modal-title" id="modalHapusLabel"><i class="bi bi-exclamation-triangle me-2"></i> Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus transaksi <strong id="hapusNama"></strong>?</p>
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

<!-- Script -->
<script>
    document.querySelectorAll('.toggle-eye').forEach(icon => {
        const target = document.getElementById(icon.dataset.target);
        if (!target.hasAttribute('data-hidden')) {
            target.dataset.hidden = 'true';
        }
        icon.addEventListener('click', () => {
            const hidden = target.dataset.hidden === 'true';
            target.textContent = hidden ? target.dataset.real : 'Rp •••••••';
            target.dataset.hidden = hidden ? 'false' : 'true';
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    });

    document.getElementById('searchInput').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('hapusId').value = this.dataset.id;
            document.getElementById('hapusNama').textContent = this.dataset.name;
            document.getElementById('formHapus').action = `/cash/${this.dataset.id}/delete`;
        });
    });
</script>