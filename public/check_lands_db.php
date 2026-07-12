<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query('SELECT id_lahan, nama_lahan, ST_AsText(geom) as geom, luas, latitude, longitude FROM lands');
if ($res->num_rows == 0) {
    echo "No lands found in database.\n";
} else {
    while($row = $res->fetch_assoc()){
        print_r($row);
    }
}
