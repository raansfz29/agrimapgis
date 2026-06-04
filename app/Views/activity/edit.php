<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .edit-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 100%);
        border-radius: 24px;
        padding: 36px 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .edit-hero::after {
        content: '';
        position: absolute;
        right: -50px; bottom: -50px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        pointer-events: none;
    }
    .rejection-banner {
        background: linear-gradient(135deg, #fff1f2 0%, #fee2e2 100%);
        border: 1.5px solid #fecdd3;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 28px;
    }
    .premium-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        margin-bottom: 24px;
    }
    .form-label {
        font-size: 10px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: block;
    }
    .form-control, .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        transition: border 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 3px rgba(217,119,6,0.12);
        outline: none;
    }
    .btn-save {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 16px 32px;
        font-size: 15px;
        font-weight: 800;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(217,119,6,0.35); }
    .btn-cancel {
        background: #f1f5f9;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 32px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-top: 10px;
    }
    .photo-slot {
        aspect-ratio: 1;
        border: 2px dashed #d97706;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #d97706;
        font-size: 18px;
        transition: all 0.2s;
        background: #fffbeb;
    }
    .photo-slot:hover { background: #fef3c7; transform: scale(1.04); }
    .photo-slot.filled { border-style: solid; border-color: #d97706; }
    #locationMap { height: 220px; border-radius: 16px; }
    .valid-badge {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 10px 15px;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
        transition: all 0.3s ease;
    }
    .old-photo-thumb {
        width: 100%;
        border-radius: 12px;
        object-fit: cover;
        max-height: 180px;
        border: 2px solid #e2e8f0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Header -->
<div class="edit-hero shadow-sm">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div style="font-size:11px; font-weight:800; opacity:0.7; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
                <i class="fas fa-pen-to-square me-2"></i>Perbaiki Aktivitas · #<?= $activity['id_aktivitas'] ?>
            </div>
            <h2 class="fw-800 mb-2" style="font-size:26px;">Revisi & Submit Ulang</h2>
            <p class="mb-0 fw-600" style="opacity:0.8; font-size:14px;">
                Perbarui data sesuai catatan penolakan PPL, lalu kirim ulang untuk diverifikasi.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('activity/detail/' . $activity['id_aktivitas']) ?>"
               class="btn fw-800 rounded-3 px-4 py-3"
               style="background:rgba(255,255,255,0.15); color:white; border:1px solid rgba(255,255,255,0.25);">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Detail
            </a>
        </div>
    </div>
</div>

<!-- Rejection Note Banner (always shown) -->
<?php if (!empty($rejectionNote)): ?>
<div class="rejection-banner">
    <div class="d-flex align-items-center gap-3 mb-2">
        <div class="rounded-3 p-2" style="background:#fecdd3;">
            <i class="fas fa-exclamation-circle text-danger fs-5"></i>
        </div>
        <div>
            <span class="fw-800 text-danger" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Catatan Penolakan PPL</span>
            <p class="mb-0 text-muted" style="font-size:11px; font-weight:600;">Harap perbaiki aktivitas Anda sesuai catatan di bawah ini sebelum submit ulang.</p>
        </div>
    </div>
    <p class="mb-0 fw-700" style="color:#991b1b; font-size:14px; line-height:1.7; padding-left: 56px;">
        <?= nl2br(esc($rejectionNote)) ?>
    </p>
</div>
<?php else: ?>
<div class="rejection-banner">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-info-circle text-danger"></i>
        <span class="fw-700 text-danger" style="font-size:13px;">Aktivitas ini ditolak. Silakan perbarui data dan submit ulang.</span>
    </div>
</div>
<?php endif; ?>

<!-- Flash Errors -->
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert rounded-4 mb-4 fw-700" style="background:#fee2e2; color:#991b1b; border:none;">
    <i class="fas fa-exclamation-circle me-2"></i>
    <?php foreach (session()->getFlashdata('errors') as $err): ?>
        <div><?= esc($err) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="post" action="<?= base_url('activity/update/' . $activity['id_aktivitas']) ?>" enctype="multipart/form-data" id="editForm">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="POST">

    <div class="row g-4">
        <!-- Left: Form Fields -->
        <div class="col-md-7">
            <div class="premium-card">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-3 p-3" style="background:#fef3c7; color:#d97706;">
                        <i class="fas fa-clipboard-list fs-5"></i>
                    </div>
                    <h6 class="fw-800 mb-0">Data Aktivitas</h6>
                </div>

                <div class="row g-3">
                    <!-- Lahan -->
                    <div class="col-12">
                        <label class="form-label">LAHAN</label>
                        <select name="id_lahan" class="form-select" id="lahanSelect" required>
                            <option value="">Pilih Lahan...</option>
                            <?php foreach ($lands as $land): ?>
                                <option value="<?= $land['id_lahan'] ?>"
                                    <?= ($land['id_lahan'] == $activity['id_lahan']) ? 'selected' : '' ?>
                                    data-lat="<?= $land['latitude'] ?>"
                                    data-lng="<?= $land['longitude'] ?>">
                                    <?= esc($land['nama_lahan']) ?> (<?= number_format($land['luas'], 1) ?> ha)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Jenis Aktivitas -->
                    <div class="col-12">
                        <label class="form-label">JENIS AKTIVITAS</label>
                        <select name="jenis_aktivitas" class="form-select" id="jenisAktivitasEdit" required>
                            <?php
                            $jenisOptions = ['Pemupukan NPK', 'Penyemprotan Pestisida', 'Penanaman', 'panen', 'Irigasi', 'Pengolahan Tanah', 'Pemeliharaan'];
                            foreach ($jenisOptions as $opt):
                            ?>
                                <option value="<?= $opt ?>" <?= ($activity['jenis_aktivitas'] === $opt) ? 'selected' : '' ?>>
                                    <?= $opt === 'panen' ? 'Pemanenan (Panen)' : ucwords($opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tanggal (readonly) -->
                    <div class="col-12">
                        <label class="form-label">TANGGAL</label>
                        <input type="date" name="tanggal" class="form-control bg-light"
                               value="<?= date('Y-m-d') ?>" readonly style="cursor:not-allowed;">
                        <span class="text-muted small mt-1 d-block" style="font-size:11px;">
                            <i class="fas fa-info-circle me-1"></i>Tanggal otomatis diperbarui ke hari ini.
                        </span>
                    </div>

                    <!-- Dosis & Metode -->
                    <div class="col-md-6" id="editDosisField">
                        <label class="form-label">DOSIS / VOLUME</label>
                        <input type="text" name="dosis" id="editDosisInput" class="form-control"
                               placeholder="Contoh: 150 kg"
                               value="<?= esc($parsedDosis) ?>" required>
                    </div>
                    <div class="col-md-6" id="editMetodeField">
                        <label class="form-label">METODE</label>
                        <input type="text" name="metode" id="editMetodeInput" class="form-control"
                               placeholder="Contoh: Tabur merata"
                               value="<?= esc($parsedMetode) ?>" required>
                    </div>

                    <!-- Hasil Panen (tampil saat panen dipilih) -->
                    <div class="col-12" id="editHarvestField" style="display:none;">
                        <div class="p-4 rounded-4 border" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-color:#bbf7d0 !important;">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:14px;">
                                    <i class="fas fa-wheat-awn"></i>
                                </div>
                                <div>
                                    <div class="fw-800 text-success" style="font-size:13px;">Data Hasil Panen</div>
                                    <div class="text-muted" style="font-size:11px;">Wajib diisi saat mencatat aktivitas pemanenan</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">TOTAL PRODUKTIVITAS HASIL PANEN</label>
                                    <input type="number" step="0.01" min="0" name="hasil_panen" id="editHasilPanen"
                                           class="form-control fw-800" placeholder="Contoh: 3.75"
                                           value="<?= esc($activity['hasil_panen'] ?? '') ?>"
                                           style="font-size:18px; text-align:center;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">SATUAN</label>
                                    <select name="satuan" class="form-select fw-800">
                                        <option value="Ton" <?= ($activity['satuan'] ?? '') === 'Ton' ? 'selected' : '' ?>>Ton</option>
                                        <option value="Kg" <?= ($activity['satuan'] ?? '') === 'Kg' ? 'selected' : '' ?>>Kilogram (Kg)</option>
                                        <option value="Kwintal" <?= ($activity['satuan'] ?? '') === 'Kwintal' ? 'selected' : '' ?>>Kwintal</option>
                                        <option value="Karung" <?= ($activity['satuan'] ?? '') === 'Karung' ? 'selected' : '' ?>>Karung</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="col-12">
                        <label class="form-label">CATATAN / DESKRIPSI</label>
                        <textarea name="deskripsi" class="form-control" rows="4"
                                  placeholder="Jelaskan detail aktivitas yang diperbaiki..." required><?= esc($parsedCatatan) ?></textarea>
                    </div>

                    <!-- Photo Upload -->
                    <div class="col-12">
                        <label class="form-label" id="photoLabel">FOTO BUKTI BARU (opsional — ganti foto lama)</label>
                        <?php if (!empty($activity['foto'])): ?>
                        <div class="mb-2 d-flex align-items-center gap-3">
                            <img src="<?= base_url('uploads/' . $activity['foto']) ?>"
                                 class="old-photo-thumb" alt="Foto lama"
                                 onerror="this.src='https://placehold.co/300x200/f8fafc/64748b?text=Gagal+Memuat'">
                            <div class="text-muted small fw-700">
                                <i class="fas fa-image me-1 text-success"></i>Foto lama akan dipertahankan jika tidak ada foto baru.
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="photo-grid" id="photoGrid">
                            <div class="photo-slot" onclick="triggerPhotoUpload(0)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(1)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(2)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(3)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(4)"><i class="fas fa-plus"></i></div>
                        </div>
                        <input type="file" name="foto" id="fotoInput" style="display:none;" accept="image/*" onchange="handlePhotoSelect(this)">
                        <span class="text-muted small mt-1 d-block" style="font-size:11px;">
                            <i class="fas fa-info-circle me-1"></i>Upload foto baru jika diminta oleh PPL.
                        </span>
                    </div>

                    <!-- Submit / Cancel -->
                    <div class="col-12 mt-3">
                        <div class="row g-2">
                            <div class="col-md-7">
                                <button type="submit" class="btn-save" id="submitBtn">
                                    <i class="fas fa-paper-plane"></i> Kirim Ulang untuk Verifikasi
                                </button>
                            </div>
                            <div class="col-md-5">
                                <a href="<?= base_url('activity/detail/' . $activity['id_aktivitas']) ?>" class="btn-cancel">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Location Verification -->
        <div class="col-md-5">
            <div class="premium-card">
                <h6 class="fw-800 mb-4">Verifikasi Lokasi</h6>
                <div id="locationMap"></div>
                <div class="valid-badge" id="locationBadge">
                    <i class="fas fa-spinner fa-spin text-secondary fs-5"></i>
                    <span>Mendeteksi posisi GPS Anda...</span>
                </div>
                <input type="hidden" name="latitude" id="latInput">
                <input type="hidden" name="longitude" id="lngInput">
            </div>

            <div class="premium-card" style="background: linear-gradient(135deg,#f0fdf4,#dcfce7); border:1.5px solid #bbf7d0;">
                <h6 class="fw-800 mb-3" style="color:#166534;"><i class="fas fa-lightbulb me-2"></i>Tips Perbaikan</h6>
                <ul class="list-unstyled mb-0" style="font-size:13px; font-weight:600; color:#166534; line-height:2;">
                    <li><i class="fas fa-check-circle me-2 opacity-60"></i>Baca catatan penolakan PPL dengan seksama</li>
                    <li><i class="fas fa-check-circle me-2 opacity-60"></i>Pastikan berada di lahan saat submit</li>
                    <li><i class="fas fa-check-circle me-2 opacity-60"></i>Upload foto baru jika diminta</li>
                    <li><i class="fas fa-check-circle me-2 opacity-60"></i>Isi semua data dengan lengkap dan akurat</li>
                    <li><i class="fas fa-check-circle me-2 opacity-60"></i>PPL akan dinotifikasi secara otomatis</li>
                </ul>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ── Jenis Aktivitas Toggle (Dosis/Metode vs Hasil Panen) ────────────
    const jenisEditSelect   = document.getElementById('jenisAktivitasEdit');
    const editHarvestField  = document.getElementById('editHarvestField');
    const editHasilInput    = document.getElementById('editHasilPanen');
    const editDosisField    = document.getElementById('editDosisField');
    const editMetodeField   = document.getElementById('editMetodeField');
    const editDosisInput    = document.getElementById('editDosisInput');
    const editMetodeInput   = document.getElementById('editMetodeInput');

    function toggleEditHarvestField() {
        const isPanen = ['panen', 'pemanenan'].includes(jenisEditSelect.value.toLowerCase());
        editHarvestField.style.display = isPanen ? 'block' : 'none';
        if (editHasilInput) editHasilInput.required = isPanen;
        editDosisField.style.display  = isPanen ? 'none' : '';
        editMetodeField.style.display = isPanen ? 'none' : '';
        if (editDosisInput)  editDosisInput.required  = !isPanen;
        if (editMetodeInput) editMetodeInput.required = !isPanen;
        if (isPanen) {
            if (editDosisInput  && !editDosisInput.value)  editDosisInput.value  = '-';
            if (editMetodeInput && !editMetodeInput.value) editMetodeInput.value = '-';
        } else {
            if (editDosisInput  && editDosisInput.value  === '-') editDosisInput.value  = '';
            if (editMetodeInput && editMetodeInput.value === '-') editMetodeInput.value = '';
        }
    }
    jenisEditSelect.addEventListener('change', toggleEditHarvestField);
    toggleEditHarvestField(); // run on load

    // Lands data for geofencing
    const landsData = <?= json_encode(array_map(function($l) {
        return [
            'id_lahan'  => $l['id_lahan'],
            'nama_lahan'=> $l['nama_lahan'],
            'latitude'  => $l['latitude'],
            'longitude' => $l['longitude'],
            'geojson'   => $l['geojson'] ?? null,
        ];
    }, $lands)) ?>;

    // Init map
    const initLat  = <?= $lands[0]['latitude']  ?? -5.385 ?>;
    const initLng  = <?= $lands[0]['longitude'] ?? 105.259 ?>;
    var map    = L.map('locationMap', { zoomControl: false }).setView([initLat, initLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.control.zoom({ position: 'topright' }).addTo(map);

    var marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);
    var circle = L.circle([initLat, initLng], { radius: 100, color: '#d97706', fillColor: '#d97706', fillOpacity: 0.12 }).addTo(map);

    var selectedLandCoords = null;
    var lastStatus = 'none';

    // Set initial land coords from selected option
    (function() {
        var sel = document.getElementById('lahanSelect');
        var opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.lat && opt.dataset.lng) {
            selectedLandCoords = { lat: parseFloat(opt.dataset.lat), lng: parseFloat(opt.dataset.lng) };
            circle.setLatLng([selectedLandCoords.lat, selectedLandCoords.lng]);
            map.setView([selectedLandCoords.lat, selectedLandCoords.lng], 16);
        }
    })();

    function getDistance(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function updateBadge(dist) {
        var badge = document.getElementById('locationBadge');
        var icon  = badge.querySelector('i');
        var text  = badge.querySelector('span');
        if (!selectedLandCoords) {
            badge.style.background = '#fffbeb'; badge.style.color = '#b45309';
            icon.className = 'fas fa-info-circle text-warning fs-5';
            text.innerText = 'Posisi diperoleh • Menunggu pemilihan lahan...';
            return;
        }
        if (dist <= 100) {
            badge.style.background = '#f0fdf4'; badge.style.color = '#166534';
            icon.className = 'fas fa-check-circle text-success fs-5';
            text.innerText = `Posisi valid • ${Math.round(dist)}m dari lahan`;
            circle.setStyle({ color: '#166534', fillColor: '#166534' });
        } else {
            badge.style.background = '#fef2f2'; badge.style.color = '#991b1b';
            icon.className = 'fas fa-exclamation-triangle text-danger fs-5';
            text.innerText = `Posisi diluar radius • ${Math.round(dist)}m dari lahan`;
            circle.setStyle({ color: '#ef4444', fillColor: '#ef4444' });
        }
    }

    function updatePos(lat, lng) {
        document.getElementById('latInput').value = lat;
        document.getElementById('lngInput').value = lng;
        if (selectedLandCoords) {
            updateBadge(getDistance(lat, lng, selectedLandCoords.lat, selectedLandCoords.lng));
        } else {
            updateBadge(Infinity);
        }
    }

    document.getElementById('lahanSelect').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.lat && opt.dataset.lng) {
            selectedLandCoords = { lat: parseFloat(opt.dataset.lat), lng: parseFloat(opt.dataset.lng) };
            circle.setLatLng([selectedLandCoords.lat, selectedLandCoords.lng]);
            var pos = marker.getLatLng();
            updatePos(pos.lat, pos.lng);
        } else {
            selectedLandCoords = null;
        }
    });

    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            map.setView([lat, lng], 17);
            marker.setLatLng([lat, lng]);
            updatePos(lat, lng);
        }, function(err) {
            console.warn('Geolocation error:', err.message);
        }, { enableHighAccuracy: true });
    }

    marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        updatePos(pos.lat, pos.lng);
    });

    // Photo Upload Logic
    let activeSlotIndex = 0;
    function triggerPhotoUpload(index) {
        activeSlotIndex = index;
        document.getElementById('fotoInput').click();
    }
    function handlePhotoSelect(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const slots = document.querySelectorAll('.photo-slot');
                slots[activeSlotIndex].innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
                slots[activeSlotIndex].classList.add('filled');
                document.getElementById('photoLabel').innerText = `FOTO BUKTI BARU (${document.querySelectorAll('.photo-slot.filled').length}/5)`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<?= $this->endSection() ?>
