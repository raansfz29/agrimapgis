<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM lands");
echo "Lands table data:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "\nTotal lands: " . $result->num_rows . "\n";

$conn->close();
?>