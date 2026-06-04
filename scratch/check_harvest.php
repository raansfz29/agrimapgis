<?php
$pdo = new PDO('mysql:host=localhost;dbname=agrimapgis', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== LANDS STATUS ===\n";
$stmt = $pdo->query('SELECT id_lahan, nama_lahan, komoditas, status_fase, luas FROM lands');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo $r['id_lahan'] . ' | ' . $r['nama_lahan'] . ' | ' . $r['komoditas'] . ' | fase: ' . $r['status_fase'] . ' | ' . $r['luas'] . " ha\n";
}

echo "\n=== ACTIVITIES TABLE COLUMNS ===\n";
$stmt = $pdo->query('DESCRIBE activities');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo $r['Field'] . ' | ' . $r['Type'] . ' | null:' . $r['Null'] . ' | default:' . $r['Default'] . "\n";
}

echo "\n=== PANEN ACTIVITIES ===\n";
$stmt = $pdo->query("SELECT id_aktivitas, jenis_aktivitas, deskripsi, status, tanggal FROM activities WHERE jenis_aktivitas LIKE '%panen%' OR jenis_aktivitas = 'panen' LIMIT 10");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo $r['id_aktivitas'] . ' | ' . $r['jenis_aktivitas'] . ' | status:' . $r['status'] . ' | tgl:' . $r['tanggal'] . "\n";
    echo '  desc: ' . substr($r['deskripsi'], 0, 100) . "\n";
}

echo "\n=== CHECK hasil_panen COLUMN ===\n";
try {
    $stmt = $pdo->query("SELECT id_aktivitas, hasil_panen FROM activities WHERE hasil_panen IS NOT NULL LIMIT 5");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        echo $r['id_aktivitas'] . ' | hasil_panen: ' . $r['hasil_panen'] . "\n";
    }
    echo "Column hasil_panen EXISTS\n";
} catch (Exception $e) {
    echo "Column hasil_panen MISSING: " . $e->getMessage() . "\n";
}
