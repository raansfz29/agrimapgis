<?php
require 'vendor/autoload.php';
$config = new \Config\Database();
$db = $config->connect();
$users = $db->table('users')->get()->getResultArray();
foreach ($users as $user) {
    echo 'Email: ' . $user['email'] . ' | Role: ' . $user['role'] . PHP_EOL;
}
