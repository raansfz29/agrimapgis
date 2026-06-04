<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$sql = "UPDATE lands SET status_bencana = 'normal' WHERE status_bencana = 'darurat'";
if ($db->query($sql) === TRUE) {
    echo "Record updated successfully. " . $db->affected_rows . " rows affected.";
} else {
    echo "Error updating record: " . $db->error;
}
$db->close();
?>
