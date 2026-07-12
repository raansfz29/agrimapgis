<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query('SELECT id_kelompok, nama_kelompok, id_ppl FROM farmer_groups');
while($row = $res->fetch_assoc()){
    print_r($row);
}
