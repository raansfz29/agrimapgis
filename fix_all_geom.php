<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

$res = $db->query("SELECT id_lahan, ST_AsText(geom) as wkt FROM lands");
$fixed = 0;
while ($row = $res->fetch_assoc()) {
    $wkt = $row['wkt'];
    if (!$wkt) continue;
    preg_match('/POLYGON\(\((.*?)\)\)/', $wkt, $matches);
    if (isset($matches[1])) {
        $coords = explode(',', $matches[1]);
        $flippedCoords = [];
        $needsFlipping = false;
        foreach ($coords as $coord) {
            $parts = explode(' ', trim($coord));
            if (count($parts) == 2) {
                // If the first part is negative (latitude) and second part is > 100 (longitude), we must flip
                if ($parts[0] < 0 && $parts[1] > 100) {
                    $flippedCoords[] = $parts[1] . ' ' . $parts[0];
                    $needsFlipping = true;
                } else {
                    $flippedCoords[] = $parts[0] . ' ' . $parts[1];
                }
            }
        }
        if ($needsFlipping) {
            $newWkt = "POLYGON((" . implode(',', $flippedCoords) . "))";
            $db->query("UPDATE lands SET geom = ST_GeomFromText('$newWkt') WHERE id_lahan = " . $row['id_lahan']);
            $fixed++;
        }
    }
}
echo "Fixed $fixed lands.\n";
