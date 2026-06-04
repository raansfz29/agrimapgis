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

    $activityModel = new App\Models\ActivityModel();

    // Test data similar to what the form would send
    $data = [
        'id_lahan' => 1,
        'id_user' => 2,
        'jenis_aktivitas' => 'Test Activity',
        'tanggal' => date('Y-m-d'),
        'deskripsi' => 'Test description',
        'status' => 'pending'
    ];

    echo "Testing activity save...\n";
    echo "Data: " . json_encode($data) . "\n\n";

    $insertedId = $activityModel->insert($data);

    if ($insertedId) {
        echo "✅ SUCCESS: Activity inserted with ID: $insertedId\n";

        // Check what was inserted
        $inserted = $activityModel->find($insertedId);
        echo "Inserted data: " . json_encode($inserted) . "\n\n";

        // Count total activities
        $total = $activityModel->countAll();
        echo "Total activities in database: $total\n";
    } else {
        echo "❌ FAILED: Could not insert activity\n";
        echo "Model errors: " . json_encode($activityModel->errors()) . "\n";

        // Check database errors
        $db = \Config\Database::connect();
        echo "Last database error: " . $db->error()['message'] . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>