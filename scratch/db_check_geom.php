<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$result = $conn->query("SELECT id_lahan, nama_lahan, ST_AsText(geom) as wkt, ST_AsGeoJSON(geom) as geojson FROM lands");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_lahan'] . " | NAME: " . $row['nama_lahan'] . " | WKT: " . $row['wkt'] . " | GEOJSON: " . substr($row['geojson'], 0, 100) . "...\n";
}
$conn->close();
?>
