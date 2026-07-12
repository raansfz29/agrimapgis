<?php
$content = file_get_contents('agrimapgis_production_mariadb.sql');
preg_match_all("/ST_GeomFromText\('POLYGON\(\((.*?)\)\)'\)/", $content, $matches);
foreach ($matches[1] as $index => $wkt) {
    $coords = explode(',', $wkt);
    $first = trim($coords[0]);
    $parts = explode(' ', $first);
    if (count($parts) == 2) {
        echo "Match $index: " . $parts[0] . ", " . $parts[1] . "\n";
    }
}
