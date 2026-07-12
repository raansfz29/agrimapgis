<?php

namespace App\Controllers;

class Fixgeom extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // 1. UPDATE LAND AREAS TO MATCH LOCAL
        $db->query("UPDATE lands SET luas = 0.8100 WHERE id_lahan = 1;");
        $db->query("UPDATE lands SET luas = 14.8700 WHERE id_lahan = 3;");
        $db->query("UPDATE lands SET luas = 23.0000 WHERE id_lahan = 4;");
        $db->query("UPDATE lands SET luas = 26.3500 WHERE id_lahan = 5;");
        $db->query("UPDATE lands SET luas = 10.9800 WHERE id_lahan = 6;");
        $db->query("UPDATE lands SET luas = 8.5500 WHERE id_lahan = 7;");
        $db->query("UPDATE lands SET luas = 20.5000 WHERE id_lahan = 8;");
        $db->query("UPDATE lands SET luas = 12.3700 WHERE id_lahan = 9;");
        $db->query("UPDATE lands SET luas = 16.1600 WHERE id_lahan = 10;");
        $db->query("UPDATE lands SET luas = 44.7600 WHERE id_lahan = 11;");
        $db->query("UPDATE lands SET luas = 45.4500 WHERE id_lahan = 12;");
        
        // 2. FIX GEOMETRIES
        $query = $db->query("SELECT id_lahan, ST_AsText(geom) as wkt FROM lands");
        $lands = $query->getResultArray();
        
        $fixedCount = 0;
        
        foreach ($lands as $land) {
            $wkt = $land['wkt'];
            if (!$wkt) continue;
            
            preg_match('/POLYGON\(\((.*?)\)\)/', $wkt, $matches);
            if (isset($matches[1])) {
                $coords = explode(',', $matches[1]);
                $flippedCoords = [];
                $needsFlipping = false;
                
                foreach ($coords as $coord) {
                    $parts = explode(' ', trim($coord));
                    if (count($parts) == 2) {
                        // Check if it's Lat Lng (Lat is negative, Lng is ~105)
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
                    
                    $db->query("UPDATE lands SET geom = ST_GeomFromText(?) WHERE id_lahan = ?", [$newWkt, $land['id_lahan']]);
                    $fixedCount++;
                }
            }
        }
        
        echo "<h1>Perbaikan Selesai!</h1>";
        echo "<p>Berhasil menyinkronkan 11 data luas lahan dengan data lokal Anda.</p>";
        echo "<p>Berhasil memperbaiki (flip) koordinat untuk {$fixedCount} lahan.</p>";
        echo "<p>Silakan kembali ke <a href='" . base_url('dashboard') . "'>Dashboard</a> dan cek Peta GIS Anda.</p>";
    }
}
