<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Setting password for Budi Santoso...\n";

// Hash the password
$password = 'budi123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = '$hashedPassword' WHERE email = 'budi@agrimapgis.test'";

if ($conn->query($sql) === TRUE) {
    echo "✅ Password set successfully for Budi Santoso\n";
    echo "Email: budi@agrimapgis.test\n";
    echo "Password: budi123\n";
} else {
    echo "❌ Failed to set password: " . $conn->error . "\n";
}

$conn->close();
?>