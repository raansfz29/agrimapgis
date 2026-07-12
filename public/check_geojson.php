<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query('SELECT ST_AsGeoJSON(geom, 6, 0) as geojson FROM lands LIMIT 1');
$row = $res->fetch_assoc();
echo $row['geojson'];
