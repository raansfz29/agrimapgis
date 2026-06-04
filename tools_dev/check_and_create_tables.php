<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SHOW TABLES");
echo "Tables in agrimapgis database:" . PHP_EOL;
$tables = [];
while($row = $result->fetch_array()) {
    echo "- " . $row[0] . PHP_EOL;
    $tables[] = $row[0];
}
$result->close();

$requiredTables = ['farmer_groups', 'users', 'lands', 'activities'];
$missingTables = array_diff($requiredTables, $tables);

if (empty($missingTables)) {
    echo PHP_EOL . "All required tables exist!" . PHP_EOL;
} else {
    echo PHP_EOL . "Missing tables: " . implode(', ', $missingTables) . PHP_EOL;
    
    // Try to create missing tables
    if (in_array('activities', $missingTables)) {
        echo "Creating activities table..." . PHP_EOL;
        $sql = "CREATE TABLE IF NOT EXISTS activities (
            id_aktivitas INT AUTO_INCREMENT PRIMARY KEY,
            id_lahan INT NOT NULL,
            id_user INT NOT NULL,
            jenis_aktivitas VARCHAR(50) NOT NULL,
            tanggal DATE NOT NULL,
            deskripsi TEXT,
            foto VARCHAR(255),
            koordinat VARCHAR(255),
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_lahan) REFERENCES lands(id_lahan) ON DELETE CASCADE,
            FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        if ($conn->query($sql) === TRUE) {
            echo "Activities table created successfully!" . PHP_EOL;
        } else {
            echo "Error creating activities table: " . $conn->error . PHP_EOL;
        }
    }
}

$conn->close();
?>