<?php
require_once 'vendor/autoload.php';

try {
    $activityModel = new App\Models\ActivityModel();
    echo "ActivityModel loaded successfully\n";

    // Try to get activities
    $activities = $activityModel->findAll();
    echo "Found " . count($activities) . " activities in database\n";

    // Try to insert a test activity
    $testData = [
        'id_lahan' => 1,
        'id_user' => 2,
        'jenis_aktivitas' => 'Test Activity',
        'tanggal' => date('Y-m-d'),
        'deskripsi' => 'Test description',
        'status' => 'pending'
    ];

    $insertedId = $activityModel->insert($testData);
    if ($insertedId) {
        echo "Test activity inserted successfully with ID: $insertedId\n";

        // Check again
        $activities = $activityModel->findAll();
        echo "Now found " . count($activities) . " activities in database\n";
    } else {
        echo "Failed to insert test activity\n";
    }

    echo "Activities table is working correctly!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>