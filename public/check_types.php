<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query("SELECT DISTINCT jenis_aktivitas FROM activities");
while($row = $res->fetch_assoc()) echo $row['jenis_aktivitas'] . "\n";
