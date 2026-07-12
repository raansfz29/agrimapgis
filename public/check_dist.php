<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query("SELECT VERSION()");
echo "Version: " . $res->fetch_assoc()['VERSION()'] . "\n";

$pt = json_encode(['type'=>'Point', 'coordinates'=>[105.23279, -5.355715]]);
$res2 = $db->query("SELECT ST_Distance(ST_GeomFromText('POINT(105.23279 -5.355715)'), ST_GeomFromGeoJSON('$pt', 2, 4326)) as d");
if($res2) echo "Dist: " . $res2->fetch_assoc()['d'] . "\n"; else echo "Error: " . $db->error . "\n";

$res3 = $db->query("SELECT ST_Distance(ST_GeomFromText('POINT(105.23279 -5.355715)'), ST_GeomFromGeoJSON('$pt')) as d");
if($res3) echo "Dist No Args: " . $res3->fetch_assoc()['d'] . "\n"; else echo "Error No Args: " . $db->error . "\n";
