<?php
$sessionPath = session_save_path();
echo "Session save path: $sessionPath\n";
echo "Path exists: " . (file_exists($sessionPath) ? "Yes" : "No") . "\n";
echo "Path writable: " . (is_writable($sessionPath) ? "Yes" : "No") . "\n";

// Check CodeIgniter session config
echo "\nCodeIgniter session config:\n";
echo "Session driver: FileHandler\n";
echo "Session expiration: 7200 seconds (2 hours)\n";
echo "Session cookie name: ci_session\n";

// Check if writable/session directory exists
$writableSessionPath = __DIR__ . '/writable/session';
echo "\nCodeIgniter writable session path: $writableSessionPath\n";
echo "Path exists: " . (file_exists($writableSessionPath) ? "Yes" : "No") . "\n";
echo "Path writable: " . (is_writable($writableSessionPath) ? "Yes" : "No") . "\n";
?>