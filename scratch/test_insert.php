<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
CodeIgniter\Boot::bootTest($paths);

$landModel = new \App\Models\LandModel();
$data = [
    'id_kelompok' => 1,
    'id_user'     => 1,
    'nama_lahan'  => 'Test Lahan Script',
    'komoditas'   => 'padi',
    'alamat'      => 'Test Alamat',
    'luas'        => 1.5,
    'status_fase' => 'persiapan'
];
$geojson = '{"type":"Polygon","coordinates":[[[105.259, -5.385], [105.260, -5.385], [105.260, -5.386], [105.259, -5.386], [105.259, -5.385]]]}';

try {
    $id = $landModel->insertLandWithGeoJSON($data, $geojson);
    echo "SUCCESS: ID " . $id;
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
