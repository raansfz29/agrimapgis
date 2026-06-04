<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$result = $conn->query("SELECT * FROM lands");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_lahan'] . " | NAME: " . $row['nama_lahan'] . " | STATUS: '" . $row['status_bencana'] . "' | FASE: " . $row['status_fase'] . "\n";
}
$conn->close();
?>
