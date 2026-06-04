<?php
$conn = mysqli_connect("localhost", "root", "", "agrimapgis");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$res = mysqli_query($conn, "DESCRIBE lands");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM lands");
$row = mysqli_fetch_assoc($res);
echo "TOTAL_LANDS: " . $row['count'] . "\n";

mysqli_close($conn);
