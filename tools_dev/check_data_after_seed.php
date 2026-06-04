<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check lands table
$result = $conn->query("SELECT id_lahan, nama_lahan FROM lands");
echo "Lands in database:" . PHP_EOL;
while($row = $result->fetch_assoc()) {
    echo "  ID: " . $row['id_lahan'] . ", Name: " . $row['nama_lahan'] . PHP_EOL;
}
$result->close();

// Check users table
$result2 = $conn->query("SELECT id_user, nama, email, role FROM users");
echo PHP_EOL . "Users in database:" . PHP_EOL;
while($row = $result2->fetch_assoc()) {
    echo "  ID: " . $row['id_user'] . ", Name: " . $row['nama'] . ", Email: " . $row['email'] . ", Role: " . $row['role'] . PHP_EOL;
}
$result2->close();

$conn->close();
?>