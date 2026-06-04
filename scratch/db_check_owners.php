<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$result = $conn->query("SELECT id_lahan, nama_lahan, id_kelompok, id_user FROM lands");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_lahan'] . " | NAME: " . $row['nama_lahan'] . " | KELOMPOK: " . $row['id_kelompok'] . " | USER: " . $row['id_user'] . "\n";
}
$conn->close();
?>
