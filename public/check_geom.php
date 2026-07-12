<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query("SELECT id_lahan, ST_AsText(geom) as wkt, ST_SRID(geom) as srid FROM lands LIMIT 1");
$row = $res->fetch_assoc();
echo "SRID: " . $row['srid'] . "\n";
echo "WKT: " . substr($row['wkt'], 0, 100) . "...\n";
