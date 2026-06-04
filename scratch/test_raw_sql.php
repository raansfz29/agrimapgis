<?php
$conn = mysqli_connect("localhost", "root", "", "agrimapgis");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$id_kelompok = 1;
$id_user = 1;
$nama_lahan = 'Test Raw';
$komoditas = 'padi';
$alamat = 'Test Alamat';
$luas = 1.5;
$geojson = '{"type":"Polygon","coordinates":[[[105.259, -5.385], [105.260, -5.385], [105.260, -5.386], [105.259, -5.386], [105.259, -5.385]]]}';

// Test 1: ST_GeomFromGeoJSON
$sql = "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, luas, geom) 
        VALUES ($id_kelompok, $id_user, '$nama_lahan', '$komoditas', '$alamat', $luas, ST_GeomFromGeoJSON('$geojson'))";

if (mysqli_query($conn, $sql)) {
    echo "TEST_1_SUCCESS\n";
} else {
    echo "TEST_1_FAIL: " . mysqli_error($conn) . "\n";
}

// Test 2: ST_SRID
$sql2 = "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, luas, geom) 
         VALUES ($id_kelompok, $id_user, 'Test 2', '$komoditas', '$alamat', $luas, ST_SRID(ST_GeomFromGeoJSON('$geojson'), 4326))";

if (mysqli_query($conn, $sql2)) {
    echo "TEST_2_SUCCESS\n";
} else {
    echo "TEST_2_FAIL: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
