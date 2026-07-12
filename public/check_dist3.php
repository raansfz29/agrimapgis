<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query("SELECT VERSION()");
echo "Version: " . $res->fetch_assoc()['VERSION()'] . "\n";

$lon = 105.23279;
$lat = -5.355715;

$ptStr = "POINT($lon $lat)";
$sql = "SELECT ST_Distance(geom, ST_GeomFromText('$ptStr')) as d FROM lands LIMIT 1";
$res2 = $db->query($sql);
if($res2) echo "Dist: " . $res2->fetch_assoc()['d'] . "\n"; else echo "Error: " . $db->error . "\n";
