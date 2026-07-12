<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

function debugPred($db, $id_lahan) {
    echo "=== LAND $id_lahan ===\n";
    $land = $db->query("SELECT * FROM lands WHERE id_lahan = $id_lahan")->fetch_assoc();
    
    // 1. Get latest panen date
    $panen = $db->query("SELECT MAX(tanggal) as tgl FROM activities WHERE id_lahan = $id_lahan AND jenis_aktivitas='panen' AND status='approved'")->fetch_assoc()['tgl'];
    
    // 2. Get latest tanam date
    $tanamQ = "SELECT MAX(tanggal) as tgl FROM activities WHERE id_lahan = $id_lahan AND jenis_aktivitas IN ('tanam','penanaman') AND status='approved'";
    if ($panen) $tanamQ .= " AND tanggal > '$panen'";
    $tanam = $db->query($tanamQ)->fetch_assoc()['tgl'];
    
    echo "Latest Panen: " . ($panen ?? 'None') . "\n";
    echo "Latest Tanam: " . ($tanam ?? 'None') . "\n";
    
    $validTanam = null;
    $source = '';
    
    if ($tanam) {
        $validTanam = $tanam;
        $source = 'aktivitas_tanam';
    } else {
        // Fallback for current cycle (after latest panen)
        if (in_array($land['status_fase'], ['tanam', 'pemeliharaan', 'panen'])) {
            $afterDate = $panen ? $panen : '1970-01-01';
            $firstAct = $db->query("SELECT MIN(tanggal) as tgl FROM activities WHERE id_lahan = $id_lahan AND status='approved' AND tanggal > '$afterDate' AND LOWER(jenis_aktivitas) NOT LIKE '%panen%' AND LOWER(jenis_aktivitas) NOT LIKE '%bencana%'")->fetch_assoc()['tgl'];
            
            if ($firstAct) {
                $validTanam = $firstAct;
                $source = 'aktivitas_pertama_siklus_baru';
            } else {
                $validTanam = 'from created_at';
                $source = 'status_fase';
            }
        }
    }
    
    echo "Result Tanam: " . ($validTanam ?? 'NULL') . " (Source: $source)\n\n";
}

debugPred($db, 2);
debugPred($db, 4);

$db->close();
