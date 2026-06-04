<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS messages (
    id_pesan INT AUTO_INCREMENT PRIMARY KEY, 
    id_pengirim INT, 
    id_penerima INT, 
    isi_pesan TEXT, 
    is_read TINYINT(1) DEFAULT 0, 
    created_at DATETIME
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'messages' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
