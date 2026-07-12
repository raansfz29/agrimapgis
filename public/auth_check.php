<?php
// Simple script to scan all controllers for endpoints that don't check session
$controllersDir = __DIR__ . '/app/Controllers';
$files = glob($controllersDir . '/*.php');

foreach ($files as $file) {
    if (basename($file) === 'BaseController.php' || basename($file) === 'Auth.php' || basename($file) === 'Landing.php' || basename($file) === 'Trace.php') continue;
    
    $content = file_get_contents($file);
    preg_match_all('/public\s+function\s+([a-zA-Z0-9_]+)\s*\(/', $content, $matches);
    
    foreach ($matches[1] as $method) {
        if ($method === '__construct' || $method === 'initController') continue;
        
        // Find the method body
        $pattern = '/public\s+function\s+' . $method . '\s*\(.*?\)\s*{(.*?)}/s';
        if (preg_match($pattern, $content, $bodyMatches)) {
            $body = $bodyMatches[1];
            if (strpos($body, 'session()->get(\'is_logged_in\')') === false && strpos($body, 'session()->get("is_logged_in")') === false) {
                // Warning! Missing auth check!
                echo "WARNING: " . basename($file) . " -> $method() may be missing session check!\n";
            }
        }
    }
}
echo "Auth check scan complete.\n";
