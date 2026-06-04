<?php echo $this->extend('layouts/premium'); ?>

<?php echo $this->section('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .activation-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
    }

    .land-preview-box {
        background: #f8fafc;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid var(--border-color);
    }

    .info-label {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 15px;
    }

    #landMap {
        height: 300px;
        border-radius: 20px;
        border: 1px solid var(--border-color);
    }

    .form-premium-label {
        font-weight: 800;
        font-size: 13px;
        color: var(--text-dark);
        margin-bottom: 10px;
        display: block;
    }

    .input-premium {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
    }

    .input-premium:focus {
        background: white;
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        outline: none;
    }

    .btn-activate-emergency {
        background: linear-gradient(135deg, #ef4444 0%, #be123c 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 16px;
        font-weight: 800;
        font-size: 15px;
        box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
        transition: all 0.3s;
    }

    .btn-activate-emergency:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(239, 68, 68, 0.3);
        color: white;
    }

    .warning-box {
        background: #fff1f2;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #fecdd3;
        color: #9f1239;
    }
</style>
<?php echo $this->endSection(); ?>

<?php echo $this->section('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('landMap', { zoomControl: false }).setView([-5.37, 105.25], 14);

        L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains:['mt0','mt1','mt2','mt3']
        }).addTo(map);

        // Fetch land data for specific ID
        fetch('<?php echo base_url('map/api-lands'); ?>?id=<?php echo $land['id_lahan']; ?>')
            .then(res => res.json())
            .then(data => {
                if (data.features && data.features.length > 0) {
                    var geojson = L.geoJSON(data, {
                        style: {
                            fillColor: '#ef4444',
                            weight: 3,
                            opacity: 1,
                            color: 'white',
                            fillOpacity: 0.4
                        }
                    }).addTo(map);
                    map.fitBounds(geojson.getBounds(), { padding: [50, 50] });
                }
            });
    });
</script>
<?php echo $this->endSection(); ?>

<?php echo $this->section('content'); ?>
<div class="row mb-5 align-items-center">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('disaster'); ?>" class="text-decoration-none text-muted">Mitigasi Bencana</a></li>
                <li class="breadcrumb-item active fw-700">Aktivasi Darurat</li>
            </ol>
        </nav>
        <h3 class="fw-800 mb-0">Aktivasi Darurat Bencana</h3>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="activation-card h-100">
            <h5 class="fw-800 mb-4">Informasi Lahan Terdampak</h5>
            
            <div class="land-preview-box mb-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="info-label">Nama Lahan</div>
                            <div class="info-value"><?php echo esc($land['nama_lahan']); ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Komoditas & Luas</div>
                            <div class="info-value"><?php echo strtoupper(esc($land['komoditas'])); ?> • <?php echo esc($land['luas']); ?> Ha</div>
                        </div>
                        <div class="mb-0">
                            <div class="info-label">Status Fase</div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-800 mt-1"><?php echo strtoupper(esc($land['status_fase'])); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="info-label">Alamat / Lokasi</div>
                            <div class="info-value small"><?php echo esc($land['alamat'] ?: 'Rajabasa, Bandar Lampung'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="landMap"></div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="activation-card">
            <h5 class="fw-800 mb-4">Form Laporan Kejadian</h5>

            <!-- Flash Errors -->
            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert rounded-4 mb-4 fw-700" style="background:#fee2e2; color:#991b1b; border:none;">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <div><?php echo esc($err); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert rounded-4 mb-4 fw-700" style="background:#fee2e2; color:#991b1b; border:none;">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo esc(session()->getFlashdata('error')); ?>
            </div>
            <?php endif; ?>
            
            <form action="<?php echo base_url('disaster/activate/' . $land['id_lahan']); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                
                <div class="mb-4">
                    <label class="form-premium-label">DESKRIPSI KERUSAKAN</label>
                    <textarea name="deskripsi_bencana" class="form-control input-premium" rows="5" placeholder="Jelaskan detail bencana, penyebab, dan estimasi tingkat kerusakan lahan..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-premium-label">UNGGAH FOTO BUKTI (DOKUMENTASI)</label>
                    <input type="file" name="foto_bencana" class="form-control input-premium" accept="image/*" required>
                    <div class="mt-2 text-muted x-small fw-600">Maksimal ukuran file 2MB (JPG, PNG). Foto harus menampilkan kondisi lahan saat ini.</div>
                </div>

                <div class="warning-box mb-4">
                    <div class="d-flex gap-3">
                        <i class="fas fa-circle-info fs-5 mt-1"></i>
                        <div>
                            <h6 class="fw-800 mb-1 small">Perhatian Penting</h6>
                            <p class="mb-0 x-small fw-600 opacity-90">Aktivasi status darurat akan membekukan aktivitas rutin lahan dan memberikan notifikasi prioritas kepada PPL dan Admin Dinas Pertanian.</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-5">
                    <button type="submit" class="btn-activate-emergency flex-grow-1">
                        <i class="fas fa-exclamation-triangle me-2"></i> AKTIFKAN STATUS DARURAT
                    </button>
                    <a href="<?php echo base_url('disaster'); ?>" class="btn btn-light rounded-3 px-4 py-3 fw-800 text-muted" style="border-radius: 16px;">
                        BATAL
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 11px; }
</style>
<?php echo $this->endSection(); ?>