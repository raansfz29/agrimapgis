<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$result = $conn->query("SELECT id_lahan, nama_lahan, latitude, longitude, geom IS NULL as geom_null FROM lands");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_lahan'] . " | NAME: " . $row['nama_lahan'] . " | LAT: " . $row['latitude'] . " | LNG: " . $row['longitude'] . " | GEOM_NULL: " . $row['geom_null'] . "\n";
}
$conn->close();
?>
