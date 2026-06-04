<?php

// Load CodeIgniter
define('FCPATH', __DIR__ . '/../public/');
require_once __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';
$app = CodeIgniter\Boot::bootWeb($paths);

$db = \Config\Database::connect();
$user = $db->table('users')->where('role', 'petani')->limit(1)->get()->getRowArray();

if (!$user) {
    echo "No farmer found.";
    exit;
}

$id_user = $user['id_user'];
$id_kelompok = $user['id_kelompok'];

// Disaster Land GeoJSON (somewhere in Rajabasa)
$geojson = json_encode([
    'type' => 'Polygon',
    'coordinates' => [[
        [105.245, -5.375],
        [105.250, -5.375],
        [105.250, -5.380],
        [105.245, -5.380],
        [105.245, -5.375]
    ]]
]);

$sql = "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, status_fase, status_bencana, deskripsi_bencana, tanggal_bencana, geom, luas) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ST_GeomFromGeoJSON(?, 2, 4326), ?)";

$db->query($sql, [
    $id_kelompok,
    $id_user,
    'Sawah Blok C - Terkena Banjir',
    'padi',
    'Jl. Rajabasa Raya, Samping Sungai',
    'panen',
    'darurat',
    'Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.',
    date('Y-m-d H:i:s'),
    $geojson,
    1.45
]);

echo "Dummy disaster land created successfully.";
