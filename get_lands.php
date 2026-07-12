<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

$res = $db->query("SELECT id_lahan, nama_lahan FROM lands");
while($row = $res->fetch_assoc()) { 
    echo $row['id_lahan'] . " - " . $row['nama_lahan'] . "\n"; 
}
