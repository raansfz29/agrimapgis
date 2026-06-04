<?php
require 'public/index.php';

$landModel = new \App\Models\LandModel();
$id = 1; // Assuming id 1 or something, let's just find one darurat
$land = $landModel->where('status_bencana', 'darurat')->first();

if ($land) {
    echo "Found darurat land ID " . $land['id_lahan'] . "\n";
    $updated = $landModel->setDisasterStatus($land['id_lahan'], 'normal');
    echo "Updated: " . var_export($updated, true) . "\n";
    print_r($landModel->errors());
} else {
    echo "No darurat land found. Simulating an update on an existing land to darurat then normal.\n";
    $landModel->setDisasterStatus(1, 'darurat', 'Test');
    $updated = $landModel->setDisasterStatus(1, 'normal');
    echo "Updated normal: " . var_export($updated, true) . "\n";
    print_r($landModel->errors());
}
?>
