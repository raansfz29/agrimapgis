<?php
$host = 'localhost';
$db   = 'agrimapgis';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $stmt = $pdo->prepare("SELECT nama, email, password FROM users WHERE nama LIKE ?");
     $stmt->execute(['%Amelia%']);
     $user = $stmt->fetch();

     if ($user) {
         echo "User Found:\n";
         echo "Nama: " . $user['nama'] . "\n";
         echo "Email: " . $user['email'] . "\n";
         echo "Password: " . $user['password'] . "\n";
     } else {
         echo "User 'Amelia' not found.\n";
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
