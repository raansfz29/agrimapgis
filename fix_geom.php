<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

// Delete the corrupted row
$db->query("DELETE FROM lands WHERE nama_lahan = 'Sawah Tunas Harapan'");

// Insert properly using INSERT ... SELECT
$sql = "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at, geom) 
        SELECT id_kelompok, id_user, 'Sawah Tunas Harapan', komoditas, alamat, 45.45, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at, geom 
        FROM lands WHERE id_lahan = 1";

$db->query($sql);
echo "Re-inserted Sawah Tunas Harapan properly with geometry.\n";

$res = $db->query("SELECT SUM(luas) as tot FROM lands");
$row = $res->fetch_assoc();
echo "Total luas sekarang: " . $row['tot'] . "\n";
