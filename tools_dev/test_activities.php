<?php
require_once 'vendor/autoload.php';

$activityModel = new App\Models\ActivityModel();
$activities = $activityModel->findAll();
echo 'Found ' . count($activities) . ' activities' . PHP_EOL;

foreach ($activities as $activity) {
    echo '- ' . $activity['jenis_aktivitas'] . ' (' . $activity['status'] . ')' . PHP_EOL;
}
?>