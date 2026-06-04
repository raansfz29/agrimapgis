<?php
// Simulate Dashboard.php data retrieval
$userRole = 'petani';
$idKelompok = 1;

$conn = new mysqli("localhost", "root", "", "agrimapgis");

$sql = "SELECT id_lahan, id_kelompok, nama_lahan, komoditas, status_fase, luas, created_at FROM lands WHERE id_kelompok = $idKelompok ORDER BY created_at DESC";
$result = $conn->query($sql);
$lands = [];
while($row = $result->fetch_assoc()) {
    $lands[] = $row;
}

$farmerLandsGeoJSON = [];
foreach ($lands as $land) {
    // getLandsGeoJSON
    $id_lahan = $land['id_lahan'];
    $geoSql = "SELECT id_lahan, id_kelompok, id_user, nama_lahan, komoditas, status_fase, status_bencana, alamat, luas, latitude, longitude, created_at, ST_AsGeoJSON(geom, 6, 2) as geojson FROM lands WHERE id_lahan = $id_lahan";
    $geoRes = $conn->query($geoSql)->fetch_assoc();
    if ($geoRes) $farmerLandsGeoJSON[] = $geoRes;
}

echo "landsData JSON string:\n";
echo json_encode($farmerLandsGeoJSON, JSON_PRETTY_PRINT) . "\n";

$conn->close();
?>
