<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SHOW TABLES");
echo "Tables in agrimapgis database:" . PHP_EOL;
while($row = $result->fetch_array()) {
    echo "- " . $row[0] . PHP_EOL;
}
$result->close();

// Check if activities table exists
$result2 = $conn->query("SHOW TABLES LIKE 'activities'");
if ($result2->num_rows > 0) {
    echo PHP_EOL . "Activities table EXISTS!" . PHP_EOL;

    // Check structure
    $result3 = $conn->query("DESCRIBE activities");
    echo "Activities table structure:" . PHP_EOL;
    while($row = $result3->fetch_assoc()) {
        echo "  " . $row['Field'] . " - " . $row['Type'] . PHP_EOL;
    }
    $result3->close();

    // Check data
    $result4 = $conn->query("SELECT COUNT(*) as count FROM activities");
    $row = $result4->fetch_assoc();
    echo PHP_EOL . "Records in activities table: " . $row['count'] . PHP_EOL;
    $result4->close();
} else {
    echo PHP_EOL . "Activities table does NOT exist!" . PHP_EOL;
}
$result2->close();
$conn->close();
?>