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
}

$conn->close();
?>