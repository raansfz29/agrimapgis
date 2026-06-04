<?php
$conn = mysqli_connect("localhost", "root", "", "agrimapgis");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$res = mysqli_query($conn, "SELECT id_lahan, nama_lahan, ST_AsGeoJSON(geom) as gj FROM lands ORDER BY id_lahan DESC LIMIT 5");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id_lahan'] . " | NAME: " . $row['nama_lahan'] . " | GEOJSON: " . substr($row['gj'], 0, 100) . "...\n";
}

mysqli_close($conn);
