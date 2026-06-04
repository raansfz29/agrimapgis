<?php
// Simple test of login logic
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Testing login functionality...\n\n";

// Test user lookup
$email = 'admin@agrimapgis.test';
$result = $conn->query("SELECT * FROM users WHERE email = '$email'");

if ($result && $row = $result->fetch_assoc()) {
    echo "✅ User found: {$row['nama']} ({$row['email']})\n";

    // Test password verification
    $password = 'admin123';
    if (password_verify($password, $row['password'])) {
        echo "✅ Password verification successful\n";

        // Simulate session data that would be set
        $sessionData = [
            'id_user'      => $row['id_user'],
            'nama'         => $row['nama'],
            'email'        => $row['email'],
            'role'         => $row['role'],
            'id_kelompok'  => $row['id_kelompok'],
            'is_logged_in' => true
        ];

        echo "✅ Session data prepared: " . json_encode($sessionData) . "\n";

    } else {
        echo "❌ Password verification failed\n";
    }
} else {
    echo "❌ User not found\n";
}

$conn->close();
echo "\nTest completed.\n";
?>