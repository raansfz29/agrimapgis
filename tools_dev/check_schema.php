<?php
$conn = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $conn->query('DESCRIBE farmer_groups');
echo "FARMER_GROUPS:\n";
while($row = $res->fetch_assoc()) { print_r($row); }
echo "\nUSERS:\n";
$res = $conn->query('DESCRIBE users');
while($row = $res->fetch_assoc()) { print_r($row); }
