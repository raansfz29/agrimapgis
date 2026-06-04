<?php
$mysqli = new mysqli("localhost", "root", "", "agrimapgis");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS messages (
    id_pesan INT AUTO_INCREMENT PRIMARY KEY,
    id_pengirim INT NOT NULL,
    id_penerima INT NOT NULL,
    isi_pesan TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($mysqli->query($sql) === TRUE) {
    echo "Table 'messages' created successfully.";
} else {
    echo "Error creating table: " . $mysqli->error;
}

$mysqli->close();
?>
