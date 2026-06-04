<?php
require_once 'app/Config/Database.php';
$db = \Config\Database::connect();
try {
    $res = $db->query("SELECT ST_GeomFromGeoJSON('{\"type\":\"Point\",\"coordinates\":[0,0]}') as g")->getRow();
    echo "EXISTS";
} catch (\Exception $e) {
    echo "NOT_EXISTS: " . $e->getMessage();
}
