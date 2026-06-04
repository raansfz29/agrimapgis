<?php
$host = 'localhost';
$db   = 'agrimapgis';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass);
     
     // Reset Budi
     $passBudi = password_hash('petani123', PASSWORD_DEFAULT);
     $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
     $stmt->execute([$passBudi, 'budi@agrimapgis.test']);
     
     // Reset PPL
     $passPPL = password_hash('ppl123', PASSWORD_DEFAULT);
     $stmt->execute([$passPPL, 'ppl@agrimapgis.test']);
     
     echo "Default user passwords (Budi & PPL) have been hashed and reset successfully!\n";
} catch (\PDOException $e) {
     echo "Update failed: " . $e->getMessage();
}
