<?php
$base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
$data = base64_decode($base64);
file_put_contents('public/images/dummy-icon-192x192.png', $data);
file_put_contents('public/images/dummy-icon-512x512.png', $data);
echo "Done\n";
