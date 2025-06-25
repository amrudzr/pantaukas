<?php date_default_timezone_set('Asia/Jakarta'); ?>
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <!-- Judul (kiri) -->
            <h5 class="mb-3 fw-semibold">
                <i class="bi bi-wallet2 me-1"></i> Form Transaksi Kas
            </h5>

            <!-- Form -->
            <form method="POST" action="/cash/create" enctype="multipart/form-data">
                <!-- Tab Switch Full Width -->
                <div class="mb-3">
                    <input type="hidden" name="type" id="typeInput" value="in">
                    <div class="row g-2">
                        <div class="col-6">
                            <button type="button" class="btn btn-success w-100 active" id="tabIn">Pemasukan</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-danger w-100" id="tabOut">Pengeluaran</button>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Tanggal -->
                    <div class="col-md-6">
                        <label class="form-label">Tanggal</label>
                        <input type="datetime-local" class="form-control form-control-sm" name="payment_date"
                            value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>

                    <!-- Nominal -->
                    <div class="col-md-6">
                        <label class="form-label">Nominal</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="nominalDisplay" placeholder="100.000" autocomplete="off">
                            <input type="hidden" name="nominal" id="nominalRaw">
                        </div>
                    </div>

                    <!-- Judul -->
                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control form-control-sm" name="title" placeholder="Contoh: Aspal Jalan RT" required>
                    </div>

                    <!-- Kategori -->
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select class="form-select form-select-sm" name="id_type_cash">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($typeCashList as $item): ?>
                                <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Lampiran -->
                    <div class="col-md-12">
                        <label class="form-label">Lampiran <span class="text-muted">(opsional)</span></label>
                        <!-- Dropdown Trigger -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="lampiranDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Pilih Jenis Lampiran
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="lampiranDropdown">
                                <li><a class="dropdown-item" href="#" onclick="showAttachment('url'); return false;">Lampiran URL</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showAttachment('file'); return false;">Upload File</a></li>
                                <li id="cameraOption"><a class="dropdown-item" href="#" onclick="showAttachment('camera'); return false;">Ambil Foto Kamera</a></li>
                            </ul>
                        </div>

                        <!-- Input URL (default hidden) -->
                        <div id="attachmentUrlInput" style="display:none; min-width:250px;">
                            <input type="url" class="form-control form-control-sm mt-2" name="attachment_url" placeholder="https://..." />
                        </div>

                        <!-- Upload File -->
                        <div id="attachmentFileInput" style="display:none;">
                            <div class="mt-2" id="filePreview" style="display: none;"></div>
                            <input type="file" class="form-control form-control-sm mt-2" name="attachment_file" accept="image/*,application/pdf" onchange="checkSize(this); previewAttachment(this);" />
                        </div>

                        <!-- Kamera Langsung -->
                        <div id="attachmentCameraInput" style="display:none;">
                            <input type="file" class="form-control form-control-sm" name="attachment_camera" accept="image/*" capture="environment" />
                            <small class="text-muted d-block mt-1">Gunakan kamera belakang untuk ambil foto langsung</small>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="col-md-12">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Opsional…"></textarea>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="/cash" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-check-circle me-1"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('form').addEventListener('submit', function(e) {
            const activeTab = document.querySelector('.btn.active');
            if (activeTab.id === 'tabOut') {
                document.getElementById('typeInput').value = 'out';
            } else {
                document.getElementById('typeInput').value = 'in';
            }
        });
    });

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
        let value = this.value.replace(/\D/g, ''); // hanya angka
        nominalRaw.value = value;

        // format dengan titik
        this.value = formatRupiah(value);
    });

    function formatRupiah(angka) {
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function showAttachment(type) {
        const urlInput = document.getElementById('attachmentUrlInput');
        const fileInput = document.getElementById('attachmentFileInput');
        const cameraInput = document.getElementById('attachmentCameraInput');

        // Sembunyikan semua dulu
        [urlInput, fileInput, cameraInput].forEach(el => {
            el.style.display = 'none';
            el.querySelector('input')?.setAttribute('disabled', 'true');
        });

        // Tampilkan sesuai pilihan
        if (type === 'url') {
            urlInput.style.display = 'block';
            urlInput.querySelector('input')?.removeAttribute('disabled');
        } else if (type === 'file') {
            fileInput.style.display = 'block';
            fileInput.querySelector('input')?.removeAttribute('disabled');
        } else if (type === 'camera') {
            cameraInput.style.display = 'block';
            cameraInput.querySelector('input')?.removeAttribute('disabled');
        }
    }

    // Deteksi apakah perangkat mobile
    function isMobileDevice() {
        return /android|iphone|ipad|iPod|mobile|tablet/i.test(navigator.userAgent);
    }

    function checkSize(input) {
        const maxSize = 6 * 1024 * 1024; // 6 MB
        if (input.files[0] && input.files[0].size > maxSize) {
            alert("Ukuran file maksimal 6 MB!");
            input.value = ""; // Clear input
        }
    }

    // Sembunyikan opsi kamera jika bukan mobile
    window.addEventListener('DOMContentLoaded', () => {
        if (!isMobileDevice()) {
            const cameraOption = document.getElementById('cameraOption');
            if (cameraOption) {
                cameraOption.style.display = 'none';
            }
        }
    });

    function previewAttachment(input) {
        const preview = document.getElementById('filePreview');
        preview.innerHTML = '';
        preview.style.display = 'none';

        const file = input.files[0];
        if (!file) return;

        const fileType = file.type;

        if (fileType.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = "img-thumbnail";
                img.style.maxHeight = "150px";
                preview.innerHTML = '';
                preview.appendChild(img);
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(file);
            link.target = "_blank";
            link.textContent = "Lihat File Lampiran";
            preview.innerHTML = '';
            preview.appendChild(link);
            preview.style.display = 'block';
        }
    }
</script>