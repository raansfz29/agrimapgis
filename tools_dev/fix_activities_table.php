<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if activities table exists
$result = $conn->query("SHOW TABLES LIKE 'activities'");
if ($result->num_rows == 0) {
    echo "Activities table does not exist. Creating...\n";

    $sql = "CREATE TABLE activities (
        id_aktivitas INT AUTO_INCREMENT PRIMARY KEY,
        id_lahan INT NOT NULL,
        id_user INT NOT NULL,
        jenis_aktivitas VARCHAR(50) NOT NULL,
        tanggal DATE NOT NULL,
        deskripsi TEXT,
        foto VARCHAR(255),
        koordinat POINT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_lahan) REFERENCES lands(id_lahan) ON DELETE CASCADE,
        FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if ($conn->query($sql) === TRUE) {
        echo "Activities table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
} else {
    echo "Activities table already exists\n";
}

// Show all tables
echo "\nAll tables in database:\n";
$result = $conn->query("SHOW TABLES");
while($row = $result->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

$conn->close();
?>