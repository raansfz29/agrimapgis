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

// Check activities table specifically
$result2 = $conn->query("DESCRIBE activities");
if ($result2) {
    echo PHP_EOL . "Activities table structure:" . PHP_EOL;
    while($row = $result2->fetch_assoc()) {
        echo "  " . $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . PHP_EOL;
    }
    $result2->close();
} else {
    echo PHP_EOL . "Activities table does NOT exist!" . PHP_EOL;
}

$conn->close();
?>