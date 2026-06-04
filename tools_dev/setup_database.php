<?php
/**
 * Database Setup Script for AgriMapGIS
 * Run: php setup_database.php
 */

echo "========================================\n";
echo "AgriMapGIS Database Setup\n";
echo "========================================\n\n";

$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("FATAL ERROR: Cannot connect to database: " . $conn->connect_error . "\n");
}

echo "✓ Connected to database\n\n";

// List of SQL commands to execute
$sqls = [
    "DROP TABLE IF EXISTS activities",
    "DROP TABLE IF EXISTS lands",
    "DROP TABLE IF EXISTS users",
    "DROP TABLE IF EXISTS farmer_groups",
    
    "CREATE TABLE farmer_groups (
        id_kelompok INT AUTO_INCREMENT PRIMARY KEY,
        nama_kelompok VARCHAR(100) NOT NULL,
        ketua VARCHAR(100) NOT NULL,
        kecamatan VARCHAR(50) DEFAULT 'Rajabasa',
        created_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE users (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('petani', 'ppl', 'admin') DEFAULT 'petani',
        id_kelompok INT,
        telepon VARCHAR(20),
        created_at DATETIME,
        FOREIGN KEY (id_kelompok) REFERENCES farmer_groups(id_kelompok) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE lands (
        id_lahan INT AUTO_INCREMENT PRIMARY KEY,
        id_kelompok INT,
        nama_lahan VARCHAR(100),
        komoditas ENUM('padi', 'jagung') NOT NULL,
        geom LONGTEXT,
        luas DECIMAL(10,4) DEFAULT 0,
        status_fase ENUM('persiapan','tanam','pemeliharaan','panen','bera','darurat') DEFAULT 'persiapan',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE activities (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

$errors = [];
$success_count = 0;

foreach ($sqls as $i => $sql) {
    if ($conn->query($sql) === TRUE) {
        $success_count++;
        echo "✓ Query " . ($i + 1) . " executed successfully\n";
    } else {
        $errors[] = "Query " . ($i + 1) . " failed: " . $conn->error;
        echo "✗ Query " . ($i + 1) . " failed: " . $conn->error . "\n";
    }
}

echo "\n";

// Verify activities table
echo "Verifying activities table...\n";
$result = $conn->query("DESCRIBE activities");
if ($result && $result->num_rows > 0) {
    echo "✓ Activities table structure:\n";
    while($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    echo "\n✓✓✓ SUCCESS: Database is ready!\n";
} else {
    echo "✗ Activities table not found!\n";
}

// Insert test data
echo "\nInserting test data...\n";

$conn->query("INSERT INTO farmer_groups (nama_kelompok, ketua, kecamatan) VALUES ('Kelompok Tani Maju Jaya', 'Budi Santoso', 'Rajabasa')");
$group_id = $conn->insert_id;

$conn->query("INSERT INTO users (nama, email, password, role, id_kelompok, telepon) VALUES ('Administrator', 'admin@agrimapgis.test', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin', NULL, '081234567890')");

$conn->query("INSERT INTO users (nama, email, password, role, id_kelompok, telepon) VALUES ('Budi Santoso', 'budi@agrimapgis.test', '" . password_hash('petani123', PASSWORD_DEFAULT) . "', 'petani', $group_id, '081298765432')");

echo "✓ Test data inserted\n";

echo "\n========================================\n";
echo "Database setup complete!\n";
echo "========================================\n";

$conn->close();
?>
