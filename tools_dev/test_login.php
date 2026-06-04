<?php
// Test login functionality
require_once 'vendor/autoload.php';

use CodeIgniter\Config\Factories;
use App\Models\UserModel;

// Initialize minimal CodeIgniter
$app = require_once 'app/Config/Boot.php';
$app->initialize();

echo "Testing login functionality...\n\n";

// Test 1: Check if UserModel can find users
$userModel = new UserModel();
$user = $userModel->where('email', 'admin@agrimapgis.test')->first();

if ($user) {
    echo "✅ User found: {$user['nama']} ({$user['email']})\n";

    // Test 2: Check password verification
    if (password_verify('admin123', $user['password'])) {
        echo "✅ Password verification successful\n";

        // Test 3: Test session setting
        $session = session();
        $sessionData = [
            'id_user'      => $user['id_user'],
            'nama'         => $user['nama'],
            'email'        => $user['email'],
            'role'         => $user['role'],
            'id_kelompok'  => $user['id_kelompok'],
            'is_logged_in' => true
        ];

        $session->set($sessionData);
        echo "✅ Session data set\n";

        // Test 4: Check if session was set
        if ($session->get('is_logged_in')) {
            echo "✅ Session verification successful\n";
            echo "Session data: " . json_encode($session->get()) . "\n";
        } else {
            echo "❌ Session not set properly\n";
        }

    } else {
        echo "❌ Password verification failed\n";
    }
} else {
    echo "❌ User not found\n";
}

echo "\nTest completed.\n";
?>