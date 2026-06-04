<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$conn->query("UPDATE lands SET nama_lahan = 'Sawah Blok C' WHERE id_lahan = 3");
echo "Update successful\n";
$conn->close();
?>
