<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$geojson = '{"type":"Polygon","coordinates":[[[105.23,-5.35],[105.24,-5.35],[105.24,-5.36],[105.23,-5.36],[105.23,-5.35]]]}';

$sql = "SELECT ST_SRID(ST_GeomFromGeoJSON(?)) as srid, ST_Area(ST_GeomFromGeoJSON(?)) as area";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $geojson, $geojson);
$stmt->execute();
$res = $stmt->get_result();

$row = $res->fetch_assoc();
print_r($row);
