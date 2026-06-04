<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
$conn->query("UPDATE lands SET status_bencana = 'normal' WHERE status_bencana != 'darurat'");
echo "Updated: " . $conn->affected_rows . " rows\n";
$conn->close();
?>
