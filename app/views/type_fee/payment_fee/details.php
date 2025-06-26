<?php
$selectedMonth = $_GET['bulan'] ?? date('m');
$selectedYear = $_GET['tahun'] ?? date('Y');

// Daftar bulan
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
?>

<div class="container py-4 pt-0">
    <!-- Header + Filter -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <h5 class="fw-semibold mb-0">
            <i class="bi bi-cash-coin text-primary me-2"></i>
            Detail Iuran: <?= htmlspecialchars($details['fee_type']['name']) ?>
        </h5>

        <form method="get" class="d-flex gap-2 align-items-center">
            <select name="bulan" class="form-select form-select-sm">
                <?php foreach ($bulanList as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $selectedMonth == $val ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="tahun" class="form-select form-select-sm">
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= $currentYear - 5; $i--):
                ?>
                    <option value="<?= $i ?>" <?= $selectedYear == $i ? 'selected' : '' ?>>
                        <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>

            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" type="submit">
                <i class="bi bi-funnel"></i> Tampilkan
            </button>
        </form>
    </div>

    <!-- Ringkasan Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Total Anggota</h6>
                        <i class="bi bi-people fs-5 text-primary"></i>
                    </div>
                    <h4><?= $summary['total_members'] ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Target Per Anggota</h6>
                        <i class="bi bi-currency-exchange fs-5 text-primary"></i>
                    </div>
                    <h4>Rp <?= number_format($summary['target_nominal'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Total Target</h6>
                        <i class="bi bi-cash-stack fs-5 text-primary"></i>
                    </div>
                    <h4>Rp <?= number_format($summary['total_target'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Total Terkumpul</h6>
                        <i class="bi bi-wallet2 fs-5 text-primary"></i>
                    </div>
                    <h4>Rp <?= number_format($summary['total_paid'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Pembayaran -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Lunas</h6>
                        <i class="bi bi-check-circle fs-5 text-success"></i>
                    </div>
                    <h4>
                        <?= $summary['total_members'] - $summary['total_unpaid'] - $summary['total_debt'] ?>
                        <small class="text-muted fs-6">orang</small>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Belum Bayar</h6>
                        <i class="bi bi-x-circle fs-5 text-danger"></i>
                    </div>
                    <h4>
                        <?= $summary['total_unpaid'] ?>
                        <small class="text-muted fs-6">orang</small>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-muted mb-0">Hutang</h6>
                        <i class="bi bi-exclamation-triangle fs-5 text-warning"></i>
                    </div>
                    <h4>
                        <?= $summary['total_debt'] ?>
                        <small class="text-muted fs-6">orang</small>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
        <form method="post" action="/fee/types/<?= $details['fee_type']['id'] ?>/details/bulk-update" id="bulkUpdateForm">
            <input type="hidden" name="fee_type_id" value="<?= $details['fee_type']['id'] ?>">
            <input type="hidden" name="month" value="<?= $selectedMonth ?>">
            <input type="hidden" name="year" value="<?= $selectedYear ?>">
            <input type="hidden" name="member_ids" id="selectedMembers" value="">

            <button type="submit" class="btn btn-success shadow-sm" id="bulkUpdateBtn" disabled>
                <i class="bi bi-check-circle me-1"></i> Tandai Terpilih sebagai Lunas
            </button>
        </form>

        <button class="btn btn-outline-primary shadow-sm" id="selectAllBtn">
            <i class="bi bi-check-square me-1"></i> Pilih Semua
        </button>
    </div>

    <!-- Tabel Anggota -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <!-- Search -->
            <div class="p-3 border-bottom">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama anggota...">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th>Nama Anggota</th>
                            <th>No HP</th>
                            <th>Status Iuran</th>
                            <th>Nominal</th>
                            <th>Tanggal Bayar</th>
                            <th>Catatan</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details['members'] as $member): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input member-checkbox"
                                        value="<?= $member['member_id'] ?>"
                                        <?= $member['payment_status'] === 'paid' ? 'disabled' : '' ?>>
                                </td>
                                <td><?= htmlspecialchars($member['member_name']) ?></td>
                                <td><?= htmlspecialchars($member['member_phone']) ?></td>
                                <td>
                                    <?php if ($member['payment_status'] === 'paid'): ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php elseif ($member['payment_status'] === 'debt'): ?>
                                        <span class="badge bg-warning">Hutang</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Belum Bayar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($member['payment_status'] === 'paid'): ?>
                                        Rp <?= number_format($member['payment_nominal'], 0, ',', '.') ?>
                                    <?php elseif ($member['payment_status'] === 'debt'): ?>
                                        Rp <?= number_format($member['payment_nominal'], 0, ',', '.') ?>
                                        <small class="text-danger">(kurang Rp <?= number_format($details['fee_type']['nominal'] - $member['payment_nominal'], 0, ',', '.') ?>)</small>
                                    <?php else: ?>
                                        Rp 0
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $member['payment_date'] ? date('d/m/Y H:i', strtotime($member['payment_date'])) : '-' ?>
                                </td>
                                <td><?= $member['payment_notes'] ? htmlspecialchars($member['payment_notes']) : '-' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-member-id="<?= $member['member_id'] ?>"
                                        data-fee-type-id="<?= $member['fee_type_id'] ?>"
                                        data-payment-id="<?= $member['payment_id'] ?>"
                                        data-nominal="<?= $member['payment_nominal'] ?>"
                                        data-notes="<?= htmlspecialchars($member['payment_notes'] ?? '') ?>"
                                        data-month="<?= $selectedMonth ?>"
                                        data-year="<?= $selectedYear ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pembayaran -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Update Pembayaran Iuran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/fee/types/<?= $details['fee_type']['id'] ?>/details/update">
                <div class="modal-body">
                    <input type="hidden" name="payment_id" id="editPaymentId">
                    <input type="hidden" name="member_id" id="editMemberId">
                    <input type="hidden" name="fee_type_id" id="editFeeTypeId">
                    <input type="hidden" name="month" id="editMonth">
                    <input type="hidden" name="year" id="editYear">

                    <div class="mb-3">
                        <label for="editNominal" class="form-label">Nominal Pembayaran</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="editNominal" name="nominal" required>
                        </div>
                        <small class="text-muted">Target: Rp <?= number_format($details['fee_type']['nominal'], 0, ',', '.') ?></small>
                    </div>

                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk modal edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editPaymentId').value = this.dataset.paymentId || '';
            document.getElementById('editMemberId').value = this.dataset.memberId;
            document.getElementById('editFeeTypeId').value = this.dataset.feeTypeId;
            document.getElementById('editNominal').value = this.dataset.nominal || '';
            document.getElementById('editNotes').value = this.dataset.notes || '';
            document.getElementById('editMonth').value = this.dataset.month;
            document.getElementById('editYear').value = this.dataset.year;
        });
    });

    // Fungsi untuk pencarian
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
            const name = tr.querySelector('td:nth-child(2)').textContent.toLowerCase();
            tr.style.display = name.includes(query) ? '' : 'none';
        });
    });

    // Fungsi untuk seleksi anggota
    const checkAll = document.getElementById('checkAll');
    const memberCheckboxes = document.querySelectorAll('.member-checkbox');
    const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
    const selectedMembersInput = document.getElementById('selectedMembers');
    const selectAllBtn = document.getElementById('selectAllBtn');

    checkAll.addEventListener('change', function() {
        memberCheckboxes.forEach(checkbox => {
            if (!checkbox.disabled) {
                checkbox.checked = this.checked;
            }
        });
        updateSelectedMembers();
    });

    memberCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedMembers);
    });

    function updateSelectedMembers() {
        const selected = Array.from(document.querySelectorAll('.member-checkbox:checked')).map(cb => cb.value);
        selectedMembersInput.value = selected.join(',');
        bulkUpdateBtn.disabled = selected.length === 0;
    }

    selectAllBtn.addEventListener('click', function() {
        let allSelected = true;
        memberCheckboxes.forEach(checkbox => {
            if (!checkbox.disabled && !checkbox.checked) {
                allSelected = false;
            }
        });

        memberCheckboxes.forEach(checkbox => {
            if (!checkbox.disabled) {
                checkbox.checked = !allSelected;
            }
        });
        checkAll.checked = false;
        updateSelectedMembers();
    });
</script>