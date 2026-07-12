<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connection failed");

// Test polygon: A square roughly 100m x 100m near equator
$geojson = '{"type":"Polygon","coordinates":[[[105.0, -5.0], [105.0009, -5.0], [105.0009, -5.0009], [105.0, -5.0009], [105.0, -5.0]]]}';

$stmt = $db->prepare("SELECT ST_Area(ST_GeomFromGeoJSON(?)) as raw_area");
$stmt->bind_param('s', $geojson);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

echo "Raw Area: " . $row['raw_area'] . "\n";
echo "In Ha (raw/10000): " . ($row['raw_area'] / 10000) . "\n";
