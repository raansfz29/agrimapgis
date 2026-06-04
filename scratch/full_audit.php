<?php
$pdo = new PDO('mysql:host=localhost;dbname=agrimapgis', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== [1] LANDS ===\n";
$stmt = $pdo->query('SELECT id_lahan, nama_lahan, komoditas, status_fase, luas FROM lands ORDER BY id_lahan');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "  [{$r['id_lahan']}] {$r['nama_lahan']} | {$r['komoditas']} | fase:{$r['status_fase']} | {$r['luas']} ha\n";
}

echo "\n=== [2] ACTIVITIES - semua data ===\n";
$stmt = $pdo->query("SELECT id_aktivitas, id_lahan, id_user, jenis_aktivitas, status, hasil_panen, satuan, tanggal FROM activities ORDER BY id_aktivitas");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $hp = $r['hasil_panen'] ?? 'NULL';
    echo "  [{$r['id_aktivitas']}] lahan:{$r['id_lahan']} | {$r['jenis_aktivitas']} | status:{$r['status']} | hasil_panen:{$hp} {$r['satuan']} | {$r['tanggal']}\n";
}

echo "\n=== [3] ACTIVITIES - panen approved ===\n";
$stmt = $pdo->query("SELECT id_aktivitas, id_lahan, jenis_aktivitas, hasil_panen, satuan, status FROM activities WHERE jenis_aktivitas = 'panen' AND status = 'approved'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo count($rows) === 0 ? "  (kosong - tidak ada panen approved)\n" : '';
foreach ($rows as $r) {
    echo "  [{$r['id_aktivitas']}] lahan:{$r['id_lahan']} | hasil_panen:{$r['hasil_panen']} {$r['satuan']}\n";
}

echo "\n=== [4] ACTIVITIES - tanam approved (untuk prediksi panen) ===\n";
$stmt = $pdo->query("SELECT id_aktivitas, id_lahan, jenis_aktivitas, tanggal, status FROM activities WHERE jenis_aktivitas IN ('tanam','penanaman') AND status = 'approved' ORDER BY tanggal DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo count($rows) === 0 ? "  (kosong - tidak ada tanam approved)\n" : '';
foreach ($rows as $r) {
    echo "  [{$r['id_aktivitas']}] lahan:{$r['id_lahan']} | {$r['jenis_aktivitas']} | {$r['tanggal']} | {$r['status']}\n";
}

echo "\n=== [5] USERS ===\n";
$stmt = $pdo->query("SELECT id_user, nama, role, id_kelompok FROM users ORDER BY role, id_user");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "  [{$r['id_user']}] {$r['nama']} | {$r['role']} | kelompok:{$r['id_kelompok']}\n";
}

echo "\n=== [6] FARMER GROUPS ===\n";
$stmt = $pdo->query("SELECT id_kelompok, nama_kelompok, kecamatan FROM farmer_groups");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "  [{$r['id_kelompok']}] {$r['nama_kelompok']} | {$r['kecamatan']}\n";
}

echo "\n=== [7] NOTIFICATIONS ===\n";
$stmt = $pdo->query("SELECT COUNT(*) as total, tipe, is_read FROM notifications GROUP BY tipe, is_read ORDER BY tipe");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "  tipe:{$r['tipe']} | is_read:{$r['is_read']} | count:{$r['total']}\n";
}
echo "DONE\n";
