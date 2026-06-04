<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Try to add column, ignore error if it already exists
$conn->query("ALTER TABLE lands ADD COLUMN alamat TEXT AFTER komoditas");
echo "Done";
$conn->close();
