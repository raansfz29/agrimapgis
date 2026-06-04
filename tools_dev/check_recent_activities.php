<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT id_aktivitas, jenis_aktivitas, tanggal, status, koordinat FROM activities ORDER BY id_aktivitas DESC LIMIT 5");
echo "Recent activities:\n";
while ($row = $result->fetch_assoc()) {
    $coord = $row['koordinat'] ? substr($row['koordinat'], 0, 30) . '...' : 'NULL';
    echo "ID: {$row['id_aktivitas']}, {$row['jenis_aktivitas']}, {$row['tanggal']}, {$row['status']}, Coord: {$coord}\n";
}

$conn->close();
?>