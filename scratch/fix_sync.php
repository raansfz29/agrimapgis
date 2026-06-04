<?php
$pdo = new PDO('mysql:host=localhost;dbname=agrimapgis', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fix notif tipe kosong
$affected = $pdo->exec("UPDATE notifications SET tipe='info' WHERE tipe='' OR tipe IS NULL");
echo "Notifikasi tipe kosong diperbaiki: {$affected} baris\n";

// Fix jenis_aktivitas 'Penanaman' → perlu match dengan query tanam di LandModel
// LandModel mencari whereIn('jenis_aktivitas', ['tanam', 'penanaman'])
// Activity [1] = 'Penanaman' (kapital) — cek apakah cocok
$stmt = $pdo->query("SELECT id_aktivitas, jenis_aktivitas FROM activities WHERE LOWER(jenis_aktivitas) IN ('tanam','penanaman','panen')");
echo "\nAktivitas tanam/panen:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "  [{$r['id_aktivitas']}] jenis: '{$r['jenis_aktivitas']}'\n";
}

// Fix: pastikan 'Penanaman' bisa dideteksi oleh query WhereIn yang case-sensitive
$count = $pdo->query("SELECT COUNT(*) FROM activities WHERE jenis_aktivitas IN ('tanam','penanaman')")->fetchColumn();
echo "\nQuery exact match 'tanam'/'penanaman': {$count} baris\n";

$count2 = $pdo->query("SELECT COUNT(*) FROM activities WHERE LOWER(jenis_aktivitas) IN ('tanam','penanaman')")->fetchColumn();
echo "Query LOWER match: {$count2} baris\n";

echo "\nDone.\n";
