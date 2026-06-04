<?php
require_once 'app/Config/Database.php';
$db = \Config\Database::connect();
echo $db->getVersion();
