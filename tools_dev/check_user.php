<?php

// Load CodeIgniter bootstrapper
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . 'vendor/autoload.php';

// Bootstrap CI4
$app = \Config\Services::app();
$app->initialize();

$db = \Config\Database::connect();
$builder = $db->table('users');
$builder->like('nama', 'Amelia');
$user = $builder->get()->getRowArray();

if ($user) {
    echo "User Found:\n";
    echo "Nama: " . $user['nama'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Password (Raw/Hashed): " . $user['password'] . "\n";
} else {
    echo "User 'Amelia' not found in database.\n";
}
