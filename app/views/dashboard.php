<?php

/**
 * File: app/views/dashboard.php
 * Deskripsi: Halaman dashboard sederhana.
 * Konten ini akan dimuat di dalam layout app.php.
 */
$selectedMonth = $_GET['bulan'] ?? date('m');
$selectedYear  = $_GET['tahun'] ?? date('Y');

$totalKas         = $totalKas ?? 2500000;
$kasChangePct     = $kasChangePct ?? 0.15;
$totalIuran       = $totalIuran ?? 1350000;
$iuranChangePct   = $iuranChangePct ?? 0.1;
$jumlahAnggota    = $jumlahAnggota ?? 25;
$anggotaActive    = $anggotaActive ?? 20;
$anggotaPassive   = $anggotaPassive ?? 3;
$anggotaKeluar    = $anggotaKeluar ?? 2;

$riwayatTransaksi = $riwayatTransaksi ?? [
    ['tanggal' => '2025-06-10', 'keterangan' => 'Pembayaran iuran', 'kategori' => 'Iuran', 'jumlah' => 50000],
    ['tanggal' => '2025-06-09', 'keterangan' => 'Pembelian ATK', 'kategori' => 'Pengeluaran', 'jumlah' => 30000],
    ['tanggal' => '2025-06-08', 'keterangan' => 'Iuran bulanan', 'kategori' => 'Iuran', 'jumlah' => 50000],
];

$anggotaTunggakan = $anggotaTunggakan ?? [
    ['nama' => 'Ahmad', 'status' => 'Hutang', 'jumlah' => 50000],
    ['nama' => 'Budi', 'status' => 'Belum Bayar', 'jumlah' => 50000],
];
?>

<!-- Filter Bulan dan Tahun -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">Dashboard</h4>
    <form method="get" class="d-flex gap-2 align-items-center">
        <select name="bulan" class="form-select form-select-sm">
            <?php
            $bulanList = [
                '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
            ];
            foreach ($bulanList as $val => $label) {
                $selected = $selectedMonth == $val ? 'selected' : '';
                echo "<option value='$val' $selected>$label</option>";
            }
            ?>
        </select>

        <select name="tahun" class="form-select form-select-sm">
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                $selected = $selectedYear == $y ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>

        <button class="btn btn-sm btn-primary" type="submit">
            Tampilkan
        </button>
    </form>
</div>

<!-- Layout Dashboard -->
<div class="row g-4 mb-4">
    <!-- Total Kas -->
    <div class="col-md-4">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="text-muted mb-0">Total Kas</h6>
                    <i class="bi bi-cash-stack fs-5 text-primary"></i>
                </div>
                <h3 class="d-flex justify-content-between align-items-center">
                    <span id="kasAmount" data-real="Rp <?= number_format($totalKas, 0, ',', '.') ?>">
                        Rp •••••••
                    </span>
                    <i class="bi bi-eye toggle-eye text-secondary fs-6" role="button" data-target="kasAmount"></i>
                </h3>
                <?= pctBadge($kasChangePct) ?>
            </div>
        </div>
    </div>

    <!-- Jumlah Anggota -->
    <div class="col-md-4">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="text-muted mb-0">Jumlah Anggota</h6>
                    <i class="bi bi-people-fill fs-5 text-primary"></i>
                </div>
                <h3 class="mb-2 text-primary"><?= $jumlahAnggota ?> Orang</h3>
                <?php
                $detailArray = [
                    "Aktif: $anggotaActive",
                    "Pasif: $anggotaPassive",
                    "Keluar: $anggotaKeluar"
                ];
                ?>
                <div id="memberDetail" class="small text-muted" data-detail='<?= json_encode($detailArray) ?>'>
                    <?= $detailArray[0] ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Iuran -->
    <div class="col-md-4">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="text-muted mb-0">Total Iuran Masuk</h6>
                    <i class="bi bi-coin fs-5 text-primary"></i>
                </div>
                <h3 class="d-flex justify-content-between align-items-center">
                    <span id="iuranAmount" data-real="Rp <?= number_format($totalIuran, 0, ',', '.') ?>">
                        Rp •••••••
                    </span>
                    <i class="bi bi-eye toggle-eye text-secondary fs-6" role="button" data-target="iuranAmount"></i>
                </h3>
                <?= pctBadge($iuranChangePct) ?>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Transaksi dan Iuran -->
<div class="row g-4">
    <!-- Riwayat Transaksi -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                Riwayat Transaksi Terakhir
                <a href="/transactions" class="btn btn-sm btn-link text-decoration-none">
                    Lihat selengkapnya <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Kategori</th>
                                <th class="text-end">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($riwayatTransaksi)): ?>
                                <?php foreach ($riwayatTransaksi as $i => $trx): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($trx['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($trx['keterangan']) ?></td>
                                        <td><?= htmlspecialchars($trx['kategori']) ?></td>
                                        <td class="text-end"><?= number_format($trx['jumlah'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Anggota Tunggakan -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                Status Iuran Anggota
                <a href="/iuran/tunggakan" class="btn btn-sm btn-link text-decoration-none">
                    Lihat selengkapnya <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th class="text-end">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($anggotaTunggakan)): ?>
                                <?php foreach ($anggotaTunggakan as $i => $agt): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($agt['nama']) ?></td>
                                        <td>
                                            <?php
                                            $badge = $agt['status'] === 'Hutang' ? 'danger' : 'warning';
                                            echo "<span class='badge bg-$badge'>{$agt['status']}</span>";
                                            ?>
                                        </td>
                                        <td class="text-end">
                                            <?= isset($agt['jumlah']) ? number_format($agt['jumlah'], 0, ',', '.') : '-' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Semua anggota lunas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Toggle Eye
        document.querySelectorAll('.toggle-eye').forEach(icon => {
            const target = document.getElementById(icon.dataset.target);
            target.dataset.hidden = 'true';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
            icon.addEventListener('click', () => {
                const hidden = target.dataset.hidden === 'true';
                target.textContent = hidden ? target.dataset.real : 'Rp •••••••';
                target.dataset.hidden = hidden ? 'false' : 'true';
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });

        // Member Detail Rotator
        const memberCycler = document.getElementById('memberDetail');
        if (memberCycler) {
            const details = JSON.parse(memberCycler.dataset.detail);
            let idx = 0;
            setInterval(() => {
                idx = (idx + 1) % details.length;
                memberCycler.textContent = details[idx];
            }, 3000);
        }
    });
</script>

<?php
// Helper untuk badge persen
function pctBadge(float $pct): string
{
    $pctText = ($pct >= 0 ? '+' : '') . number_format($pct * 100, 2) . '%';
    $icon    = $pct >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right';
    $color   = $pct >= 0 ? 'text-success' : 'text-danger';
    return "<span class='d-inline-flex align-items-center small $color'><i class='bi $icon me-1'></i>$pctText</span>";
}
?>