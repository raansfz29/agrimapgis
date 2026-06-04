<?php
// Simple test using direct database connection
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Testing activity save...\n";

// Test data
$data = [
    'id_lahan' => 1,
    'id_user' => 2,
    'jenis_aktivitas' => 'Test Activity',
    'tanggal' => date('Y-m-d'),
    'deskripsi' => 'Test description',
    'status' => 'pending'
];

echo "Data: " . json_encode($data) . "\n\n";

// Insert directly into database
$sql = "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, tanggal, deskripsi, status) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iissss", $data['id_lahan'], $data['id_user'], $data['jenis_aktivitas'], $data['tanggal'], $data['deskripsi'], $data['status']);

    if ($stmt->execute()) {
        $insertedId = $conn->insert_id;
        echo "✅ SUCCESS: Activity inserted with ID: $insertedId\n";

        // Check what was inserted
        $result = $conn->query("SELECT * FROM activities WHERE id_aktivitas = $insertedId");
        if ($result && $row = $result->fetch_assoc()) {
            echo "Inserted data: " . json_encode($row) . "\n\n";
        }

        // Count total activities
        $result = $conn->query("SELECT COUNT(*) as count FROM activities");
        if ($result && $row = $result->fetch_assoc()) {
            echo "Total activities in database: " . $row['count'] . "\n";
        }
    } else {
        echo "❌ FAILED: Could not insert activity\n";
        echo "Database error: " . $conn->error . "\n";
    }

    $stmt->close();
} else {
    echo "❌ FAILED: Could not prepare statement\n";
    echo "Database error: " . $conn->error . "\n";
}

$conn->close();
?>