<?php
$pdo = new PDO('mysql:host=localhost;dbname=agrimapgis', 'root', '');
$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id_notif INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    judul VARCHAR(100) NOT NULL,
    pesan TEXT,
    tipe ENUM('info', 'warning', 'danger') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($sql);
echo "Table notifications created successfully.\n";
