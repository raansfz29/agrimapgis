<?php
$conn = mysqli_connect("localhost", "root", "", "agrimapgis");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$res = mysqli_query($conn, "SELECT id_lahan, id_kelompok, nama_lahan FROM lands");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id_lahan'] . " | GROUP: " . $row['id_kelompok'] . " | NAME: " . $row['nama_lahan'] . "\n";
}

mysqli_close($conn);
