<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

function debugPred($db, $id_lahan) {
    echo "=== LAND $id_lahan ===\n";
    $land = $db->query("SELECT * FROM lands WHERE id_lahan = $id_lahan")->fetch_assoc();
    
    // 1. Get latest panen date
    $panen = $db->query("SELECT MAX(tanggal) as tgl FROM activities WHERE id_lahan = $id_lahan AND jenis_aktivitas='panen' AND status='approved'")->fetch_assoc()['tgl'];
    
    // 2. Get latest tanam date
    $tanam = $db->query("SELECT MAX(tanggal) as tgl FROM activities WHERE id_lahan = $id_lahan AND jenis_aktivitas IN ('tanam','penanaman') AND status='approved'")->fetch_assoc()['tgl'];
    
    echo "Latest Panen: " . ($panen ?? 'None') . "\n";
    echo "Latest Tanam: " . ($tanam ?? 'None') . "\n";
    
    $validTanam = null;
    $source = '';
    
    if ($tanam && (!$panen || $tanam > $panen)) {
        $validTanam = $tanam;
        $source = 'aktivitas_tanam';
    } else {
        // Fallback for current cycle (after latest panen)
        if (in_array($land['status_fase'], ['tanam', 'pemeliharaan', 'panen'])) {
            $afterDate = $panen ? $panen : '1970-01-01';
            $firstAct = $db->query("SELECT MIN(tanggal) as tgl FROM activities WHERE id_lahan = $id_lahan AND status='approved' AND tanggal > '$afterDate' AND jenis_aktivitas != 'panen'")->fetch_assoc()['tgl'];
            
            if ($firstAct) {
                $validTanam = $firstAct;
                $source = 'aktivitas_pertama_siklus_baru';
            } else {
                // Use updated_at instead of created_at because it could be a 2 year old land
                $validTanam = date('Y-m-d', strtotime($land['updated_at']));
                $source = 'status_fase_updated';
            }
        }
    }
    
    echo "Result Tanam: " . ($validTanam ?? 'NULL') . " (Source: $source)\n\n";
}

debugPred($db, 2);
debugPred($db, 4);
debugPred($db, 21);

$db->close();
