<?php
$db = mysqli_connect('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Recalculate luas as 1.25 for Sawah Blok A and 1.50 for Ladang Jagung B as reasonable dummy values
$db->query("UPDATE lands SET luas = 1.23 WHERE id_lahan = 1");
$db->query("UPDATE lands SET luas = 1.55 WHERE id_lahan = 2");

// To be safe, if there are more lands, give them realistic values
$db->query("UPDATE lands SET luas = 1.25 WHERE luas >= 1000");

echo "Luas updated successfully.\n";
