<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connection failed");

$filename = 'agrimapgis_production_mariadb.sql';
$fp = fopen($filename, 'w');

fwrite($fp, "SET FOREIGN_KEY_CHECKS = 0;\n");
fwrite($fp, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
fwrite($fp, "SET time_zone = \"+00:00\";\n\n");

$tables = ['farmer_groups', 'users', 'lands', 'activities', 'disaster_logs', 'notifications'];

// DROP tables in REVERSE order
$reverseTables = array_reverse($tables);
foreach ($reverseTables as $table) {
    fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
}
fwrite($fp, "\n");

foreach ($tables as $table) {
    $res = $db->query("SHOW CREATE TABLE `$table`");
    $row = $res->fetch_row();
    $createTableSql = $row[1];
    
    // MariaDB compatibility
    $createTableSql = preg_replace('/ \/\*\!80003 SRID \d+ \*\//', '', $createTableSql);
    $createTableSql = str_replace('utf8mb4_0900_ai_ci', 'utf8mb4_general_ci', $createTableSql);
    
    fwrite($fp, $createTableSql . ";\n\n");
    
    $res = $db->query("SELECT * FROM `$table`");
    if ($res->num_rows > 0) {
        fwrite($fp, "INSERT INTO `$table` VALUES \n");
        $rows = [];
        
        if ($table === 'lands') {
            $res = $db->query("SELECT *, ST_AsText(geom) as geom_wkt FROM `$table`");
        }
        
        while ($r = $res->fetch_assoc()) {
            $values = [];
            foreach ($r as $key => $val) {
                if ($key === 'geom_wkt') continue;
                
                if ($table === 'lands' && $key === 'geom') {
                    if ($r['geom_wkt'] !== null) {
                        $values[] = "ST_GeomFromText('" . $db->real_escape_string($r['geom_wkt']) . "')";
                    } else {
                        $values[] = "NULL";
                    }
                } elseif ($val === null) {
                    $values[] = "NULL";
                } else {
                    $values[] = "'" . $db->real_escape_string($val) . "'";
                }
            }
            $rows[] = "(" . implode(",", $values) . ")";
        }
        fwrite($fp, implode(",\n", $rows) . ";\n\n");
    }
}

fwrite($fp, "SET FOREIGN_KEY_CHECKS = 1;\n");
fclose($fp);
echo "Custom MariaDB-compatible dump created with correct DROP order: $filename\n";
