<?php
$pdo = new PDO('mysql:host=localhost;dbname=agrimapgis', 'root', '');
$stmt = $pdo->query('SELECT id_lahan, nama_lahan, id_kelompok FROM lands');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
