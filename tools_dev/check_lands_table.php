<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error");
}

$result = $conn->query("DESCRIBE lands");
if ($result) {
    echo "Lands table structure:" . PHP_EOL;
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . PHP_EOL;
    }
    $result->close();
} else {
    echo "Could not describe lands table" . PHP_EOL;
}

$conn->close();
?>