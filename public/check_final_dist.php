<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$id_lahan = 21; // The one with SRID 4326
$longitude = 105.248916;
$latitude = -5.338793;

$sql = "SELECT ST_SRID(geom) as srid FROM lands WHERE id_lahan = $id_lahan";
$sridRow = $db->query($sql)->fetch_assoc();
$srid = $sridRow ? (int)$sridRow['srid'] : 0;

if ($srid === 4326) {
    $ptWKT = "POINT({$latitude} {$longitude})";
} else {
    $ptWKT = "POINT({$longitude} {$latitude})";
}

$sqlCheck = "SELECT ST_Contains(geom, ST_GeomFromText('{$ptWKT}', {$srid})) as is_inside,
                    IF({$srid} = 4326, 
                       ST_Distance(geom, ST_GeomFromText('{$ptWKT}', {$srid})), 
                       ST_Distance(geom, ST_GeomFromText('{$ptWKT}', {$srid})) * 111319
                    ) as distance
             FROM lands WHERE id_lahan = $id_lahan";

$res = $db->query($sqlCheck);
if ($res) {
    print_r($res->fetch_assoc());
} else {
    echo "Error: " . $db->error;
}
