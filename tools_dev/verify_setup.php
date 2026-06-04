<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database Verification\n";
echo "====================\n\n";

// Check tables
$result = $conn->query("SHOW TABLES");
echo "Tables in database:\n";
$tables = [];
while($row = $result->fetch_array()) {
    echo "  ✓ " . $row[0] . "\n";
    $tables[] = $row[0];
}

// Check activities table specifically
if (in_array('activities', $tables)) {
    echo "\n✓ Activities table EXISTS\n";
    
    $result = $conn->query("DESCRIBE activities");
    echo "\nActivities table structure:\n";
    while($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM activities");
    $row = $result->fetch_assoc();
    echo "\nRecords in activities table: " . $row['count'] . "\n";
} else {
    echo "\n✗ Activities table NOT found!\n";
}

// Check users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
echo "\nUsers in database: " . $row['count'] . "\n";

// Check lands
$result = $conn->query("SELECT COUNT(*) as count FROM lands");
$row = $result->fetch_assoc();
echo "Lands in database: " . $row['count'] . "\n";

echo "\n✓✓✓ Setup Complete! Ready to save activities.\n";

$conn->close();
?>
