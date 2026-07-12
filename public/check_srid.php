<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query("SELECT ST_SRID(geom) FROM lands WHERE id_lahan=2");
echo "SRID 2: " . $res->fetch_assoc()['ST_SRID(geom)'] . "\n";
