<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
CodeIgniter\Boot::bootTest($paths);

$db = \Config\Database::connect();
try {
    $res = $db->query("SELECT ST_GeomFromGeoJSON('{\"type\":\"Point\",\"coordinates\":[0,0]}') as g")->getRow();
    echo "EXISTS";
} catch (\Exception $e) {
    echo "NOT_EXISTS: " . $e->getMessage();
}
