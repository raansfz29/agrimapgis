<?php
/**
 * Seeder: 1 Aktivitas per kategori per lahan (Total 7 aktivitas per lahan)
 */

$db = new mysqli('localhost', 'root', '', 'agrimapgis');
if ($db->connect_error) die("Connect failed: " . $db->connect_error);

$userLandMap = [
    29 => ['id_lahan' => 1,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Sukarame 1'],
    18 => ['id_lahan' => 3,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Maju Bersama'],
    19 => ['id_lahan' => 4,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Sukamaju I'],
    20 => ['id_lahan' => 5,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Jaya Bersama'],
    21 => ['id_lahan' => 6,  'komoditas' => 'jagung', 'nama_lahan' => 'Ladang Tani Mandiri I'],
    22 => ['id_lahan' => 7,  'komoditas' => 'jagung', 'nama_lahan' => 'Ladang Tani Mandiri II'],
    23 => ['id_lahan' => 8,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Harapan Jaya'],
    24 => ['id_lahan' => 9,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Sumber Rejeki'],
    25 => ['id_lahan' => 10, 'komoditas' => 'padi',   'nama_lahan' => 'Sawah Sido Makmur'],
    26 => ['id_lahan' => 11, 'komoditas' => 'padi',   'nama_lahan' => 'Sawah Karya Tani'],
    27 => ['id_lahan' => 12, 'komoditas' => 'padi',   'nama_lahan' => 'Sawah Tunas Harapan'],
    // Petani yang kelompoknya tidak punya lahan (fallback ke lahan lain tapi id_user mereka sendiri)
    17 => ['id_lahan' => 1,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Sukarame 1'],
    28 => ['id_lahan' => 3,  'komoditas' => 'padi',   'nama_lahan' => 'Sawah Maju Bersama'],
];

$categories = [
    'pengolahan_tanah', 'penanaman', 'irigasi', 'pemupukan_npk', 
    'penyemprotan_pestisida', 'pemeliharaan', 'panen'
];

$descMap = [
    'pengolahan_tanah'       => [
        'padi'   => 'Pengolahan tanah menggunakan traktor tangan. Tanah dibajak sedalam 20-25 cm, kemudian digaru hingga rata dan siap tanam.',
        'jagung' => 'Pengolahan tanah dilakukan dengan membuat alur/guludan menggunakan traktor mini. Lahan diolah sedalam 30 cm untuk menyiapkan media tanam jagung.',
    ],
    'penanaman'              => [
        'padi'   => 'Penanaman padi sistem jajar legowo 2:1 jarak 25x12,5x50 cm. Bibit varietas Ciherang umur 21 hari, 2-3 bibit per lubang.',
        'jagung' => 'Penanaman jagung varietas Pioneer P27 jarak 75x20 cm. Setiap lubang 1-2 benih. Penanaman dilakukan setelah hujan.',
    ],
    'irigasi'                => [
        'padi'   => 'Pengairan sawah menggunakan irigasi teknis dari saluran primer. Ketinggian air dipertahankan 3-5 cm di atas permukaan tanah.',
        'jagung' => 'Penyiraman dengan pompa dari saluran irigasi terdekat. Frekuensi 2x seminggu pada musim kemarau.',
    ],
    'pemupukan_npk'          => [
        'padi'   => 'Pemupukan NPK Phonska 300 kg/ha + urea 150 kg/ha. Diaplikasikan secara larikan di antara barisan tanaman umur 14 HST.',
        'jagung' => 'Pemupukan NPK 16-16-16 dosis 350 kg/ha. Diberikan secara tugal di samping tanaman umur 10 HST, diulang umur 30 HST.',
    ],
    'penyemprotan_pestisida' => [
        'padi'   => 'Penyemprotan insektisida Decis 2,5 EC dosis 1 ml/L untuk mengendalikan wereng batang coklat dan penggerek batang padi.',
        'jagung' => 'Aplikasi herbisida Gramoxone 276 SL dosis 2 L/ha untuk mengendalikan gulma. Dilakukan sebelum tanam (pre-emergent).',
    ],
    'pemeliharaan'           => [
        'padi'   => 'Penyulaman tanaman padi yang mati atau tidak normal. Dilakukan maksimal 2 minggu setelah tanam agar pertumbuhan seragam.',
        'jagung' => 'Pembumbunan jagung umur 30 HST, penjarangan tanaman, dan pembersihan gulma antar barisan tanaman.',
    ],
    'panen'                  => [
        'padi'   => 'Panen padi umur 105-110 HST, malai menguning 90-95%. Pemanenan menggunakan mesin combine harvester.',
        'jagung' => 'Panen jagung pipilan umur 100-105 HST, biji keras dan kelobot kering. Kadar air biji saat panen 25-30%.',
    ],
];

// Clear existing activities
$db->query("DELETE FROM activities");
echo "Cleared existing activities.\n\n";

$totalInserted = 0;
$baseDate = new DateTime('2026-04-01');

foreach ($userLandMap as $idUser => $land) {
    foreach ($categories as $idx => $catKey) {
        $desc = $descMap[$catKey][$land['komoditas']];
        
        // Define statuses explicitly to have a mix for each land
        $statuses = ['approved', 'approved', 'pending', 'rejected', 'approved', 'pending', 'approved'];
        $status = $statuses[$idx]; // Assign 1 status per category
        
        $date = clone $baseDate;
        $date->modify("+" . ($idx * 7) . " days");
        $dateStr = $date->format('Y-m-d');
        
        $hasilPanen = 'NULL';
        $satuan     = 'NULL';
        if ($catKey === 'panen' && $status === 'approved') {
            $hp = ($land['komoditas'] === 'padi') ? round(rand(400, 650) / 100, 2) : round(rand(450, 750) / 100, 2);
            $hasilPanen = $hp;
            $satuan     = "'ton'";
        }

        $descEsc = $db->real_escape_string($desc);
        $sql = "INSERT INTO activities 
                    (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, status)
                VALUES 
                    ({$land['id_lahan']}, {$idUser}, '{$catKey}', {$hasilPanen}, {$satuan}, '{$dateStr}', '{$descEsc}', '{$status}')";
        
        $db->query($sql);
        if ($db->error) {
            echo "ERROR user $idUser, cat $catKey: " . $db->error . "\n";
        } else {
            $totalInserted++;
        }
    }
    echo "✓ User {$idUser} -> Land {$land['id_lahan']}: 7 activities inserted\n";
}

echo "\n=== TOTAL INSERTED: $totalInserted ===\n";

// Now, why is petani not seeing anything? Let's check lands and groups for a test user.
$testUser = 18; // Wahyudi
$res = $db->query("SELECT id_user, nama, id_kelompok FROM users WHERE id_user = $testUser");
$user = $res->fetch_assoc();
echo "\nTest User: {$user['nama']} (Group: {$user['id_kelompok']})\n";

$res = $db->query("SELECT id_lahan, nama_lahan, id_kelompok FROM lands WHERE id_kelompok = {$user['id_kelompok']}");
while($r = $res->fetch_assoc()) {
    echo "Land in Group: {$r['id_lahan']} - {$r['nama_lahan']}\n";
}
