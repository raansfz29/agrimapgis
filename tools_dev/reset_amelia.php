<?php
$host = 'localhost';
$db   = 'agrimapgis';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass);
     $new_pass = password_hash('amelia123', PASSWORD_DEFAULT);
     $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
     $stmt->execute([$new_pass, 'amelia@agrimapgis.test']);
     echo "Password for Amelia reset to 'amelia123' successfully!\n";
} catch (\PDOException $e) {
     echo "Update failed: " . $e->getMessage();
}
