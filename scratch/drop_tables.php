<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'agrimapgis');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$mysqli->query('SET FOREIGN_KEY_CHECKS = 0;');
$mysqli->query('DROP TABLE IF EXISTS activities');
$mysqli->query('DROP TABLE IF EXISTS lands');
$mysqli->query('DROP TABLE IF EXISTS users');
$mysqli->query('DROP TABLE IF EXISTS farmer_groups');
$mysqli->query('DROP TABLE IF EXISTS migrations');
$mysqli->query('SET FOREIGN_KEY_CHECKS = 1;');
echo "Tables dropped successfully.";
