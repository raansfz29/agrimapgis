<?php
$pdo = new PDO('mysql:host=localhost;dbname=agrimapgis', 'root', '');
$tables = ['lands', 'activities', 'messages', 'notifications'];
foreach ($tables as $table) {
    echo "\n--- TABLE: $table ---\n";
    try {
        $res = $pdo->query("DESCRIBE $table");
        if ($res) {
            foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row) {
                echo "{$row['Field']} ({$row['Type']}) - Default: {$row['Default']}\n";
            }
        } else {
            echo "Table not found.\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
