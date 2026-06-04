<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Adding test land data...\n";

// Insert test land data
$sql = "INSERT INTO lands (id_kelompok, nama_lahan, komoditas, geom, luas, status_fase) VALUES
(1, 'Lahan Sawah 1', 'padi', 'POINT(110.123456 -7.654321)', 0.5, 'persiapan'),
(1, 'Lahan Sawah 2', 'jagung', 'POINT(110.223456 -7.754321)', 0.3, 'tanam'),
(2, 'Lahan Sawah 3', 'padi', 'POINT(110.323456 -7.854321)', 0.7, 'pemeliharaan')";

if ($conn->query($sql) === TRUE) {
    echo "✅ SUCCESS: Test land data inserted\n";

    // Check what was inserted
    $result = $conn->query("SELECT * FROM lands");
    echo "Current lands data:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id_lahan']}, Name: {$row['nama_lahan']}, Commodity: {$row['komoditas']}, Status: {$row['status_fase']}\n";
    }
    echo "\nTotal lands: " . $result->num_rows . "\n";
} else {
    echo "❌ FAILED: Could not insert land data\n";
    echo "Database error: " . $conn->error . "\n";
}

$conn->close();
?>