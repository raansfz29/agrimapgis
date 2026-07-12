<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query("SELECT id_lahan, ST_AsText(geom) as wkt FROM lands LIMIT 5");
while($r = $res->fetch_assoc()) echo $r['id_lahan'] . ': ' . substr($r['wkt'], 0, 50) . "\n";
