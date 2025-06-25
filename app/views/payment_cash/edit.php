<?php date_default_timezone_set('Asia/Jakarta'); ?>
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h5 class="mb-3 fw-semibold">
                <i class="bi bi-wallet2 me-1"></i> Edit Transaksi Kas
            </h5>

            <div class="mb-3">
                <input type="hidden" name="type" id="typeInput" value="<?= $cash['type'] ?>">
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn w-100 <?= $cash['type'] === 'in' ? 'btn-success active' : 'btn-outline-success' ?>" id="tabIn">Pemasukan</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn w-100 <?= $cash['type'] === 'out' ? 'btn-danger active' : 'btn-outline-danger' ?>" id="tabOut">Pengeluaran</button>
                    </div>
                </div>
            </div>

            <form method="POST" action="/cash/<?= $cash['id'] ?>/edit" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal</label>
                        <input type="datetime-local" class="form-control form-control-sm" name="payment_date"
                            value="<?= date('Y-m-d\TH:i', strtotime($cash['payment_date'])) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nominal</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="nominalDisplay" placeholder="100.000" value="<?= number_format($cash['nominal'], 0, '', '.') ?>">
                            <input type="hidden" name="nominal" id="nominalRaw" value="<?= $cash['nominal'] ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control form-control-sm" name="title" value="<?= htmlspecialchars($cash['title']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select class="form-select form-select-sm" name="id_type_cash">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($typeCashList as $item): ?>
                                <option value="<?= $item['id'] ?>" <?= $item['id'] == $cash['id_type_cash'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Lampiran <span class="text-muted">(opsional)</span></label>

                        <?php
                        $isImage = @getimagesize($_SERVER['DOCUMENT_ROOT'] . $cash['attachment']);
                        $isUrl = filter_var($cash['attachment'], FILTER_VALIDATE_URL);
                        ?>

                        <?php if ($isImage): ?>
                            <div class="my-2">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" data-img="<?= $cash['attachment'] ?>">
                                    <img src="<?= $cash['attachment'] ?>" alt="Preview" class="img-thumbnail" style="max-height:150px; cursor: zoom-in;">
                                </a>
                            </div>
                        <?php elseif ($isUrl): ?>
                            <p class="my-2">
                                <a href="<?= htmlspecialchars($cash['attachment']) ?>" target="_blank">🔗 Lihat Lampiran URL</a>
                            </p>
                        <?php elseif ($cash['attachment']): ?>
                            <p class="my-2">
                                <a href="<?= $cash['attachment'] ?>" target="_blank">Buka Lampiran File</a>
                            </p>
                        <?php endif; ?>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Ganti Lampiran
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showAttachment('url'); return false;">Lampiran URL</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showAttachment('file'); return false;">Upload File</a></li>
                                <li id="cameraOption"><a class="dropdown-item" href="#" onclick="showAttachment('camera'); return false;">Ambil Foto Kamera</a></li>
                            </ul>
                        </div>

                        <div class="mt-2" id="attachmentUrlInput" style="display:none; min-width:250px;">
                            <input type="url" class="form-control form-control-sm" name="attachment_url" placeholder="https://..." disabled />
                        </div>

                        <div class="mt-2" id="attachmentFileInput" style="display:none;">
                            <input type="file" class="form-control form-control-sm" name="attachment_file" accept="image/*,application/pdf" disabled onchange="checkSize(this)" />
                        </div>

                        <div class="mt-2" id="attachmentCameraInput" style="display:none;">
                            <input type="file" class="form-control form-control-sm" name="attachment_camera" accept="image/*" capture="environment" disabled />
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control form-control-sm" name="notes" rows="2"><?= htmlspecialchars($cash['notes']) ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="/cash" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h6 class="modal-title" id="imagePreviewModalLabel">Pratinjau Gambar</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center" style="overflow: auto;">
                <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 80vh; transform: scale(1); transition: transform 0.2s ease;">
            </div>
        </div>
    </div>
</div>

<script>
    const tabIn = document.getElementById('tabIn');
    const tabOut = document.getElementById('tabOut');
    const typeInput = document.getElementById('typeInput');

    tabIn.addEventListener('click', () => {
        tabIn.classList.add('btn-success', 'active');
        tabOut.classList.remove('btn-danger', 'active');
        tabOut.classList.add('btn-outline-danger');
        tabIn.classList.remove('btn-outline-success');
        typeInput.value = 'in';
    });

    tabOut.addEventListener('click', () => {
        tabOut.classList.add('btn-danger', 'active');
        tabIn.classList.remove('btn-success', 'active');
        tabIn.classList.add('btn-outline-success');
        tabOut.classList.remove('btn-outline-danger');
        typeInput.value = 'out';
    });

    const nominalDisplay = document.getElementById('nominalDisplay');
    const nominalRaw = document.getElementById('nominalRaw');

    nominalDisplay.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        nominalRaw.value = value;
        this.value = formatRupiah(value);
    });

    function formatRupiah(angka) {
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function showAttachment(type) {
        const types = ['url', 'file', 'camera'];
        types.forEach(t => {
            const el = document.getElementById('attachment' + capitalize(t) + 'Input');
            const input = el.querySelector('input');
            el.style.display = 'none';
            input.disabled = true;
        });

        const activeEl = document.getElementById('attachment' + capitalize(type) + 'Input');
        const activeInput = activeEl.querySelector('input');
        activeEl.style.display = 'block';
        activeInput.disabled = false;
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function checkSize(input) {
        const maxSize = 6 * 1024 * 1024; // 6 MB
        if (input.files[0] && input.files[0].size > maxSize) {
            alert("Ukuran file maksimal 6 MB!");
            input.value = ""; // Clear input
        }
    }
    
    // Preview Zoom Modal
    document.addEventListener('DOMContentLoaded', () => {
        const previewImage = document.getElementById('previewImage');
        let scale = 1;

        document.querySelectorAll('[data-bs-target="#imagePreviewModal"]').forEach(link => {
            link.addEventListener('click', function() {
                const imgSrc = this.getAttribute('data-img');
                previewImage.src = imgSrc;
                previewImage.style.transform = 'scale(1)';
                scale = 1;
            });
        });

        previewImage.addEventListener('wheel', function(e) {
            e.preventDefault();
            scale += e.deltaY * -0.001;
            scale = Math.min(Math.max(.5, scale), 5);
            this.style.transform = `scale(${scale})`;
        });

        document.getElementById('imagePreviewModal').addEventListener('hidden.bs.modal', () => {
            previewImage.src = '';
            previewImage.style.transform = 'scale(1)';
        });

        if (!/android|iphone|ipad|iPod|mobile|tablet/i.test(navigator.userAgent)) {
            const cameraOption = document.getElementById('cameraOption');
            if (cameraOption) cameraOption.style.display = 'none';
        }
    });
</script>