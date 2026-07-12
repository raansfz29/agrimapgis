<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .form-header { margin-bottom: 25px; }
    .form-header h4 { font-weight: 800; margin-bottom: 5px; }
    .form-header p { color: var(--text-muted); font-size: 14px; }

    .premium-form-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }

    .form-label {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-uppercase: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .form-control, .form-select {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        background-color: white;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 4px rgba(30, 126, 52, 0.1);
    }

    .photo-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        margin-top: 15px;
    }
    .photo-slot {
        aspect-ratio: 1;
        border: 2px dashed #e2e8f0;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.2s;
        background: #f8fafc;
    }
    .photo-slot:hover { border-color: var(--primary-green); color: var(--primary-green); background: #f0fdf4; }
    .photo-slot.filled { border-style: solid; border-color: #d1fae5; background: #f0fdf4; color: var(--primary-green); }

    .btn-save { background: var(--primary-green); color: white; border: none; padding: 14px; border-radius: 15px; font-weight: 800; font-size: 15px; }
    .btn-save:hover { background: var(--dark-green); transform: translateY(-2px); }
    .btn-cancel { background: white; color: var(--text-dark); border: 1px solid #e2e8f0; padding: 14px; border-radius: 15px; font-weight: 800; font-size: 15px; }

    #locationMap {
        height: 250px;
        border-radius: 15px;
        background: #f1f5f9;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
    }

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
        transition: all 0.3s ease;
    }

    .tips-box {
        background: #f0fdf4;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #dcfce7;
    }
    .tips-list { list-style: none; padding: 0; margin: 0; }
    .tips-list li { 
        display: flex; 
        gap: 15px; 
        margin-bottom: 15px; 
        font-size: 13px; 
        color: #166534; 
        font-weight: 500; 
        line-height: 1.5;
    }
    .tips-list .num { font-weight: 800; opacity: 0.5; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="form-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h4>Catat Aktivitas</h4>
        <p class="mb-0">Lengkapi data aktivitas pertanian Anda.</p>
    </div>
    <div class="search-box" style="width: 100%; max-width: 300px;">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari lahan, aktivitas...">
        </div>
    </div>
</div>

<!-- Error Messages -->
<?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
            <div>
                <strong>Perhatian!</strong> Harap perbaiki kesalahan berikut:
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
            <div>
                <strong>Gagal!</strong> <?= session()->getFlashdata('error') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('activity/save') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-md-7">
            <div class="premium-form-card">
                <h5 class="fw-800 mb-1">Form Aktivitas</h5>
                <p class="text-muted small mb-4">Pastikan Anda berada di lokasi lahan saat mencatat.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">LAHAN</label>
                        <select name="id_lahan" class="form-select" id="lahanSelect" required>
                            <option value="">Pilih Lahan...</option>
                            <?php foreach ($lands as $land): ?>
                                <option value="<?= $land['id_lahan'] ?>"><?= esc($land['nama_lahan']) ?> (<?= number_format($land['luas'], 1) ?> ha)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (in_array(session()->get('role'), ['ppl', 'admin', 'petani'])): ?>
                    <div class="col-12">
                        <label class="form-label">PETANI PELAKSANA / BERTUGAS</label>
                        <select name="id_user" class="form-select" required>
                            <option value="">Pilih Petani Anggota...</option>
                            <?php foreach ($farmers as $farmer): ?>
                                <option value="<?= $farmer['id_user'] ?>" <?= (session()->get('id_user') == $farmer['id_user']) ? 'selected' : '' ?>><?= esc($farmer['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <label class="form-label">JENIS AKTIVITAS</label>
                        <select name="jenis_aktivitas" class="form-select" id="jenisAktivitas" required>
                            <option value="Pemupukan NPK">Pemupukan NPK</option>
                            <option value="Penyemprotan Pestisida">Penyemprotan Pestisida</option>
                            <option value="Penanaman">Penanaman</option>
                            <option value="panen">Pemanenan (Panen)</option>
                            <option value="Irigasi">Irigasi</option>
                            <option value="Pengolahan Tanah">Pengolahan Tanah</option>
                            <option value="Pemeliharaan">Pemeliharaan</option>
                        </select>
                    </div>

                    <div class="col-12" id="harvestField" style="display: none;">
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
                                    <input type="number" step="0.01" min="0" name="hasil_panen" id="hasilPanen"
                                           class="form-control fw-800" placeholder="Contoh: 3.75"
                                           style="font-size:18px; text-align:center;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">SATUAN</label>
                                    <select name="satuan" class="form-select fw-800">
                                        <option value="Ton">Ton</option>
                                        <option value="Kg">Kilogram (Kg)</option>
                                        <option value="Kwintal">Kwintal</option>
                                        <option value="Karung">Karung</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 rounded-3 text-muted d-flex align-items-center gap-2" style="background:rgba(255,255,255,0.7); font-size:12px;">
                                        <i class="fas fa-info-circle text-success"></i>
                                        Data ini digunakan untuk menghitung produktivitas lahan dan statistik kelompok tani.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">TANGGAL</label>
                        <input type="date" name="tanggal" class="form-control bg-light" value="<?= date('Y-m-d') ?>" readonly style="cursor: not-allowed;">
                        <span class="text-muted small mt-1 d-block" style="font-size: 11px;"><i class="fas fa-info-circle me-1"></i>Pencatatan aktivitas wajib bersifat real-time pada hari ini.</span>
                    </div>

                    <div class="col-md-6" id="dosisField">
                        <label class="form-label">DOSIS / VOLUME</label>
                        <input type="text" name="dosis" id="dosisInput" class="form-control" placeholder="Contoh: 150 kg" required>
                    </div>

                    <div class="col-md-6" id="metodeField">
                        <label class="form-label">METODE</label>
                        <input type="text" name="metode" id="metodeInput" class="form-control" placeholder="Contoh: Tabur merata" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">CATATAN</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Masukkan detail tambahan jika ada..." required></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <label class="form-label" id="photoLabel">FOTO BUKTI (0/5)</label>
                        <div class="photo-grid" id="photoGrid">
                            <div class="photo-slot" onclick="triggerPhotoUpload(0)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(1)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(2)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(3)"><i class="fas fa-plus"></i></div>
                            <div class="photo-slot" onclick="triggerPhotoUpload(4)"><i class="fas fa-plus"></i></div>
                        </div>
                        <!-- We use one input for simplicity in this version, but can be expanded -->
                        <input type="file" name="foto" id="fotoInput" style="display: none;" accept="image/*" onchange="handlePhotoSelect(this)">
                    </div>

                    <div class="col-12 mt-5">
                        <div class="row g-2">
                            <div class="col-md-7"><button type="submit" class="btn-save w-100">✓ Simpan Aktivitas</button></div>
                            <div class="col-md-5"><a href="<?= base_url('dashboard') ?>" class="btn btn-cancel w-100">Batal</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <!-- Location Verification -->
            <div class="premium-card mb-4">
                <h6 class="fw-800 mb-4">Verifikasi Lokasi</h6>
                <div id="locationMap"></div>
                <div class="valid-badge">
                    <i class="fas fa-spinner fa-spin text-secondary fs-5"></i>
                    <span>Mendeteksi posisi GPS Anda...</span>
                </div>
                <input type="hidden" name="latitude" id="latInput">
                <input type="hidden" name="longitude" id="lngInput">
            </div>

            <!-- Tips Section -->
            <div class="tips-box">
                <h6 class="fw-800 mb-4" style="color: #166534;">Tips Pencatatan</h6>
                <ul class="tips-list">
                    <li><span class="num">1</span> <span>Aktifkan GPS dengan akurasi tinggi.</span></li>
                    <li><span class="num">2</span> <span>Foto harus terlihat lahan & komoditas.</span></li>
                    <li><span class="num">3</span> <span>Catat segera setelah aktivitas selesai.</span></li>
                    <li><span class="num">4</span> <span>PPL akan memverifikasi dalam 1x24 jam.</span></li>
                    <li><span class="num">5</span> <span>Pastikan dosis sesuai anjuran kelompok.</span></li>
                </ul>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/dexie/dist/dexie.js"></script>
<script src="<?= base_url('js/pwa-sync.js') ?>"></script>
<script>
    // ── Harvest field toggle ─────────────────────────────────────────
    const jenisSelect  = document.getElementById('jenisAktivitas');
    const harvestField = document.getElementById('harvestField');
    const hasilInput   = document.getElementById('hasilPanen');
    const dosisField   = document.getElementById('dosisField');
    const metodeField  = document.getElementById('metodeField');
    const dosisInput   = document.getElementById('dosisInput');
    const metodeInput  = document.getElementById('metodeInput');

    function toggleHarvestField() {
        const isPanen = ['panen', 'pemanenan'].includes(jenisSelect.value.toLowerCase());

        // Panen: tampilkan hasil panen, sembunyikan dosis & metode
        harvestField.style.display = isPanen ? 'block' : 'none';
        if (hasilInput) hasilInput.required = isPanen;

        dosisField.style.display  = isPanen ? 'none' : '';
        metodeField.style.display = isPanen ? 'none' : '';
        if (dosisInput)  dosisInput.required  = !isPanen;
        if (metodeInput) metodeInput.required = !isPanen;

        // Isi nilai default agar validasi server tidak gagal saat panen
        if (isPanen) {
            if (dosisInput && !dosisInput.value)  dosisInput.value  = '-';
            if (metodeInput && !metodeInput.value) metodeInput.value = '-';
        } else {
            if (dosisInput  && dosisInput.value  === '-') dosisInput.value  = '';
            if (metodeInput && metodeInput.value === '-') metodeInput.value = '';
        }
    }
    jenisSelect.addEventListener('change', toggleHarvestField);
    toggleHarvestField(); // run on load

    // ── Form submit validation ────────────────────────────────────────
    const activityForm = document.querySelector('form');
    activityForm.addEventListener('submit', async function(e) {
        // Enforce minimal 1 photo proof
        const filledSlots = document.querySelectorAll('.photo-slot.filled');
        if (filledSlots.length === 0) {
            e.preventDefault();
            alert('Harap unggah minimal 1 foto bukti aktivitas!');
            return;
        }

        // Enforce hasil panen if panen
        const isPanen = ['panen', 'pemanenan'].includes(jenisSelect.value.toLowerCase());
        if (isPanen && hasilInput && (!hasilInput.value || parseFloat(hasilInput.value) <= 0)) {
            e.preventDefault();
            alert('Harap isi Total Produktivitas Hasil Panen (harus lebih dari 0).');
            hasilInput.focus();
            return;
        }

        if (!navigator.onLine) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {};
            const SKIP_KEYS = ['foto', 'csrf_test_name'];
            formData.forEach((value, key) => {
                if (!SKIP_KEYS.includes(key) && !key.startsWith('csrf')) {
                    data[key] = value;
                }
            });

            // Convert photo to base64 so it can be stored in IndexedDB and synced later
            const fotoFile = document.getElementById('fotoInput').files[0];
            if (fotoFile) {
                const reader = new FileReader();
                reader.onload = async function(ev) {
                    data['foto_base64'] = ev.target.result;  // e.g. "data:image/jpeg;base64,..."
                    data['foto_mime']   = fotoFile.type;
                    data['foto_name']   = fotoFile.name;
                    const saved = await saveActivityOffline(data);
                    if (saved) {
                        alert('Koneksi tidak tersedia. Aktivitas + foto telah disimpan secara OFFLINE dan akan disinkronkan otomatis saat ada internet.');
                        window.location.href = '<?= base_url('dashboard') ?>';
                    } else {
                        alert('Gagal menyimpan data offline.');
                    }
                };
                reader.readAsDataURL(fotoFile);
            } else {
                const saved = await saveActivityOffline(data);
                if (saved) {
                    alert('Koneksi tidak tersedia. Aktivitas telah disimpan secara OFFLINE (tanpa foto) dan akan disinkronkan otomatis saat ada internet.');
                    window.location.href = '<?= base_url('dashboard') ?>';
                } else {
                    alert('Gagal menyimpan data offline.');
                }
            }
        }
    });


    var map = L.map('locationMap', { zoomControl: false }).setView([-5.385, 105.259], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var marker = L.marker([-5.385, 105.259], { draggable: true }).addTo(map);
    var circle = L.circle([-5.385, 105.259], { radius: 100, color: '#ef4444', fillOpacity: 0.1 }).addTo(map);

    var lastStatus = 'outside';
    var selectedLandCoords = null;

    // Lands data passed from backend
    const landsData = <?= json_encode($lands) ?>;

    function getDistance(lat1, lon1, lat2, lon2) {
        var R = 6371000; // Radius of the earth in m
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c; // Distance in m
    }

    // Ray-casting point in polygon algorithm
    function isPointInPolygon(point, vs) {
        var x = point[0], y = point[1];
        var inside = false;
        for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
            var xi = vs[i][0], yi = vs[i][1];
            var xj = vs[j][0], yj = vs[j][1];
            var intersect = ((yi > y) != (yj > y))
                && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    }

    function checkPointInLand(lng, lat, geojsonStr) {
        if (!geojsonStr) return false;
        try {
            var geom = JSON.parse(geojsonStr);
            if (geom.type === 'Polygon') {
                return isPointInPolygon([lng, lat], geom.coordinates[0]);
            } else if (geom.type === 'MultiPolygon') {
                for (var i = 0; i < geom.coordinates.length; i++) {
                    if (isPointInPolygon([lng, lat], geom.coordinates[i][0])) {
                        return true;
                    }
                }
            }
        } catch (e) {
            console.error(e);
        }
        return false;
    }

    // Auto-select nearest or containing land based on GPS
    function autoSelectLand(lat, lng) {
        if (!landsData || landsData.length === 0) return;

        let matchedLandId = null;

        // 1. Try to find if inside any land polygon
        for (const land of landsData) {
            if (checkPointInLand(lng, lat, land.geojson)) {
                matchedLandId = land.id_lahan;
                break;
            }
        }

        // 2. If not inside, find the closest land by center coordinates
        if (!matchedLandId) {
            let minDistance = Infinity;
            let closestLandId = null;

            for (const land of landsData) {
                if (land.latitude && land.longitude) {
                    let dist = getDistance(lat, lng, parseFloat(land.latitude), parseFloat(land.longitude));
                    if (dist < minDistance) {
                        minDistance = dist;
                        closestLandId = land.id_lahan;
                    }
                }
            }

            // If the closest land is within 500 meters, we auto select it!
            if (closestLandId && minDistance <= 500) {
                matchedLandId = closestLandId;
            }
        }

        // 3. Update the dropdown selection
        if (matchedLandId) {
            var selectEl = document.getElementById('lahanSelect');
            if (selectEl.value != matchedLandId) {
                selectEl.value = matchedLandId;
                
                // Update selectedLandCoords
                var matchedLand = landsData.find(l => l.id_lahan == matchedLandId);
                if (matchedLand && matchedLand.latitude && matchedLand.longitude) {
                    selectedLandCoords = { lat: parseFloat(matchedLand.latitude), lng: parseFloat(matchedLand.longitude) };
                    circle.setLatLng([parseFloat(matchedLand.latitude), parseFloat(matchedLand.longitude)]);
                    circle.setRadius(100); // 100 meters geofencing radius matching backend
                }
            }
        }
    }

    function updatePos(lat, lng) {
        document.getElementById('latInput').value = lat;
        document.getElementById('lngInput').value = lng;
        
        // Auto select containing or nearest land
        autoSelectLand(lat, lng);
        
        var statusBadge = document.querySelector('.valid-badge');
        var statusText = statusBadge.querySelector('span');
        var icon = statusBadge.querySelector('i');

        if (selectedLandCoords) {
            var dist = getDistance(lat, lng, selectedLandCoords.lat, selectedLandCoords.lng);

            if (dist <= 100) { // 100 meters matching backend geofencing check
                if (lastStatus === 'outside') {
                    showNotification("Masuk Radius", "Anda sekarang berada di dalam lokasi lahan.");
                    lastStatus = 'inside';
                }
                statusBadge.style.background = '#f0fdf4';
                statusBadge.style.color = '#166534';
                icon.className = 'fas fa-check-circle text-success fs-5';
                statusText.innerText = `Posisi valid • ${Math.round(dist)}m dari lahan`;
                circle.setStyle({ color: '#166534', fillColor: '#166534' });
            } else {
                if (lastStatus === 'inside') {
                    showNotification("Keluar Radius", "Peringatan: Anda keluar dari radius lahan yang dipilih.");
                    lastStatus = 'outside';
                }
                statusBadge.style.background = '#fef2f2';
                statusBadge.style.color = '#991b1b';
                icon.className = 'fas fa-exclamation-triangle text-danger fs-5';
                statusText.innerText = `Posisi diluar radius • ${Math.round(dist)}m dari lahan`;
                circle.setStyle({ color: '#ef4444', fillColor: '#ef4444' });
            }
        } else {
            statusBadge.style.background = '#fffbeb';
            statusBadge.style.color = '#b45309';
            icon.className = 'fas fa-info-circle text-warning fs-5';
            statusText.innerText = 'Posisi diperoleh • Menunggu pemilihan lahan...';
        }
    }

    function showNotification(title, body) {
        if (Notification.permission === "granted") {
            new Notification(title, { body: body, icon: '<?= base_url('favicon.ico') ?>' });
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission();
        }
    }

    document.getElementById('lahanSelect').addEventListener('change', function() {
        var landId = this.value;
        if (!landId) {
            selectedLandCoords = null;
            return;
        }
        var land = landsData.find(l => l.id_lahan == landId);
        if (land && land.latitude && land.longitude) {
            selectedLandCoords = { lat: parseFloat(land.latitude), lng: parseFloat(land.longitude) };
            circle.setLatLng([parseFloat(land.latitude), parseFloat(land.longitude)]);
            circle.setRadius(100);
            
            var pos = marker.getLatLng();
            updatePos(pos.lat, pos.lng);
        }
    });

    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            map.setView([lat, lng], 17);
            marker.setLatLng([lat, lng]);
            updatePos(lat, lng);
        }, function(error) {
            console.warn("Geolocation error: ", error.message);
        }, {
            enableHighAccuracy: true
        });
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
                const activeSlot = slots[activeSlotIndex];
                
                // Show preview
                activeSlot.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 13px;">`;
                activeSlot.classList.add('filled');
                
                // Update counter
                const filledCount = document.querySelectorAll('.photo-slot.filled').length;
                document.getElementById('photoLabel').innerText = `FOTO BUKTI (${filledCount}/5)`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Request notification permission on load
    if (Notification.permission !== "granted") {
        Notification.requestPermission();
    }

    // Toggle Harvest Fields
    document.getElementById('jenisAktivitas').addEventListener('change', function() {
        const harvestField = document.getElementById('harvestField');
        if (this.value === 'panen') {
            harvestField.style.display = 'block';
        } else {
            harvestField.style.display = 'none';
        }
    });
</script>
<?= $this->endSection() ?>