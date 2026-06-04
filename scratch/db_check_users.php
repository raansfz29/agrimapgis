<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$result = $conn->query("SELECT id_user, nama, email, role, id_kelompok FROM users");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_user'] . " | NAME: " . $row['nama'] . " | EMAIL: " . $row['email'] . " | ROLE: " . $row['role'] . " | KELOMPOK: " . $row['id_kelompok'] . "\n";
}
$conn->close();
?>
