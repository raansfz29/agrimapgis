<?php

// Load CI4
require 'vendor/autoload.php';
$app = Config\Services::app();
$db = Config\Database::connect();

$query = $db->query("SELECT geom FROM lands LIMIT 1");
$row = $query->getRow();
if ($row) {
    echo "GEOM TYPE: " . gettype($row->geom) . "\n";
    echo "GEOM VALUE: " . substr($row->geom, 0, 100) . "...\n";
} else {
    echo "No data found.\n";
}
