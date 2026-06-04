<?php
$pdo = new PDO('mysql:host=localhost;dbname=agrimapgis', 'root', '');
$stmt = $pdo->query('SHOW COLUMNS FROM lands');
echo "Columns in lands table:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}
