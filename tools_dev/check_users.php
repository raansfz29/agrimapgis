<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT id_user, nama, email, role, id_kelompok FROM users");
echo "Users in database:\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id_user']}, Name: {$row['nama']}, Email: {$row['email']}, Role: {$row['role']}, Group: {$row['id_kelompok']}\n";
}
echo "\nTotal users: " . $result->num_rows . "\n";

$conn->close();
?>