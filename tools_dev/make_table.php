<?php
// Direct database connection to create activities table
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database\n";

// Drop and recreate
echo "Dropping activities table if exists...\n";
$conn->query("DROP TABLE IF EXISTS activities");

echo "Creating activities table...\n";
$sql = "CREATE TABLE activities (
    id_aktivitas INT AUTO_INCREMENT PRIMARY KEY,
    id_lahan INT NOT NULL,
    id_user INT NOT NULL,
    jenis_aktivitas VARCHAR(50) NOT NULL,
    tanggal DATE NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255),
    koordinat VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "SUCCESS: Activities table created\n";
} else {
    echo "ERROR: " . $conn->error . "\n";
    exit(1);
}

$result = $conn->query("DESCRIBE activities");
if ($result) {
    echo "Table structure:\n";
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " - " . $row['Type'] . "\n";
    }
}

$conn->close();
?>
