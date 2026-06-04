<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
CodeIgniter\Boot::bootTest($paths);

$mapController = new \App\Controllers\Map();
session()->set('is_logged_in', true);
session()->set('role', 'admin');

$response = $mapController->apiLands();
echo $response->getJSON();
