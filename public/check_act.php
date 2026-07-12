<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
echo "ACTIVITIES:\n";
$res = $db->query('SELECT * FROM activities WHERE id_lahan IN (2,4) ORDER BY id_lahan, tanggal');
while($row = $res->fetch_assoc()) {
    echo "Lahan {$row['id_lahan']} | {$row['jenis_aktivitas']} | {$row['tanggal']} | {$row['status']}\n";
}
