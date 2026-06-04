<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT id_user, nama, email, password FROM users");
echo "User passwords in database:\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id_user']}, Name: {$row['nama']}, Email: {$row['email']}, Password hash: {$row['password']}\n";
}

// Test password verification
echo "\nTesting password verification:\n";
$testPasswords = ['admin123', 'password123', 'budi123'];

foreach ($testPasswords as $testPass) {
    $result = $conn->query("SELECT id_user, nama, email, password FROM users");
    while ($row = $result->fetch_assoc()) {
        if (password_verify($testPass, $row['password'])) {
            echo "✅ Password '$testPass' matches for user: {$row['nama']} ({$row['email']})\n";
        }
    }
    $result->data_seek(0); // Reset result pointer
}

$conn->close();
?>