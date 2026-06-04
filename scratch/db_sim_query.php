<?php
// Simulate Dashboard queries for role 'petani' with id_kelompok = 1
$userRole = 'petani';
$idKelompok = 1;
$idUser = 2; // Budi Santoso

$conn = new mysqli("localhost", "root", "", "agrimapgis");

$sql = "SELECT id_lahan, id_kelompok, id_user, nama_lahan, komoditas, status_fase, luas, created_at FROM lands";
if ($userRole === 'petani') {
    $sql .= " WHERE id_kelompok = $idKelompok";
}
$result = $conn->query($sql);
echo "Simulated lands query for id_kelompok = 1:\n";
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_lahan'] . " | NAME: " . $row['nama_lahan'] . " | KELOMPOK: " . $row['id_kelompok'] . " | USER: " . $row['id_user'] . "\n";
}

$conn->close();
?>
