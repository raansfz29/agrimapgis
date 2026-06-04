<?php
define('FCPATH', __DIR__ . '/public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);
$pathsConfig = 'app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$db = \Config\Database::connect();
try {
    $model = new \App\Models\LandModel();
    $data = $model->getLandsGeoJSON(1);
    echo "GeoJSON Works!\n";
    print_r($data);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
