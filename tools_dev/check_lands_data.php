<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM lands");
if ($result) {
    echo "Lands table data:" . PHP_EOL;
    while($row = $result->fetch_assoc()) {
        echo "  ID: " . $row['id_lahan'] . ", Name: " . $row['nama_lahan'] . ", Group: " . $row['id_kelompok'] . ", Komoditas: " . $row['komoditas'] . PHP_EOL;
    }
    $result->close();
} else {
    echo "Could not query lands table" . PHP_EOL;
}

$conn->close();
?>