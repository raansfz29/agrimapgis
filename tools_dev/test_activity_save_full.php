<?php
require_once 'vendor/autoload.php';

use CodeIgniter\Config\Factories;

// Initialize CodeIgniter
$app = require_once 'app/Config/Boot.php';
$app->initialize();

try {
    // Simulate session data for a logged-in user
    $_SESSION['is_logged_in'] = true;
    $_SESSION['id_user'] = 2; // Budi Santoso
    $_SESSION['nama'] = 'Budi Santoso';
    $_SESSION['role'] = 'petani';
    $_SESSION['id_kelompok'] = 1;

    $activityModel = new ActivityModel();
    
    // Test data similar to what the form would send
    $data = [
        'id_lahan' => 1, // Should exist from seeder
        'id_user' => 2,  // Budi Santoso
        'jenis_aktivitas' => 'Test Activity',
        'tanggal' => date('Y-m-d'),
        'deskripsi' => 'Test description',
        'status' => 'pending'
    ];

    echo "Attempting to insert activity..." . PHP_EOL;
    echo "Data: " . json_encode($data) . PHP_EOL;
    
    $insertedId = $activityModel->insert($data);
    
    if ($insertedId) {
        echo "SUCCESS: Activity inserted with ID: $insertedId" . PHP_EOL;
        
        // Check what was inserted
        $inserted = $activityModel->find($insertedId);
        echo "Inserted data: " . json_encode($inserted) . PHP_EOL;
    } else {
        echo "FAILED: Could not insert activity" . PHP_EOL;
        echo "Model errors: " . json_encode($activityModel->errors()) . PHP_EOL;
        
        // Check database errors
        $db = \Config\Database::connect();
        echo "Last database error: " . $db->error()['message'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
?>