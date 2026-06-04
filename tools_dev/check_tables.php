<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$result = $conn->query("SELECT * FROM migrations");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
