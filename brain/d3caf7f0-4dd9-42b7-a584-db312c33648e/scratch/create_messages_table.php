<?php
require 'app/Config/Database.php';
$db = \Config\Database::connect();

$sql = "CREATE TABLE IF NOT EXISTS messages (
    id_pesan INT AUTO_INCREMENT PRIMARY KEY, 
    id_pengirim INT, 
    id_penerima INT, 
    isi_pesan TEXT, 
    is_read TINYINT(1) DEFAULT 0, 
    created_at DATETIME
)";

try {
    $db->query($sql);
    echo "Table 'messages' created successfully.";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
