<?php
$mysqli = new mysqli("localhost", "root", "", "agrimapgis");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

$queries = [
    // 1 (Lahan 1 -> 12, User 101 -> 17)
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (12, 17, 'Pemupukan', NULL, NULL, '2026-05-10', 'Pemupukan fase vegetatif menggunakan pupuk Urea NPK', 'pupuk_lahan1.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2295 -5.3650)'))",

    // 2 (Lahan 1 -> 12, User 101 -> 17)
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (12, 17, 'Penyemprotan Hama', NULL, NULL, '2026-05-28', 'Penyemprotan pestisida pencegahan ulat grayak', 'semprot_lahan1.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2290 -5.3645)'))",

    // 3 (Lahan 2 -> 13, User 102 -> 18)
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (13, 18, 'Penyiangan Gulma', NULL, NULL, '2026-05-15', 'Pembongkaran rumput liar di sekitar bedengan jagung', NULL, 'approved', NOW(), ST_GeomFromText('POINT(105.2420 -5.3585)'))",

    // 4 (Lahan 3 -> 14, User 103 -> 19)
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (14, 19, 'Pemanenan', 5.2, 'Ton', '2026-05-24', 'Panen raya padi sawah blok Rajabasa Jaya', 'panen_lahan3.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2495 -5.3620)'))",

    // 5 (Lahan 4 -> 15, User 104 -> 20)
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (15, 20, 'Pengolahan Lahan', NULL, NULL, '2026-06-02', 'Pembajakan tanah menggunakan traktor roda dua', 'bajak_lahan4.jpg', 'pending', NOW(), ST_GeomFromText('POINT(105.2390 -5.3530)'))",

    // 6 (Lahan 5 -> 16, User 105 -> 21)
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (16, 21, 'Penyemaian Benih', NULL, NULL, '2026-05-20', 'Penyemaian benih padi varietas Ciherang', 'semai_lahan5.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2460 -5.3680)'))",

    // 7 (Lahan 2 -> 13, User 102 -> 18)
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (13, 18, 'Pemanenan', 3.8, 'Ton', '2026-06-03', 'Panen jagung manis komoditas utama kelompok 2', 'panen_jagung.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2425 -5.3590)'))"
];

$success = 0;
foreach($queries as $index => $q) {
    if ($mysqli->query($q)) {
        $success++;
    } else {
        echo "Error on row " . ($index + 1) . ": " . $mysqli->error . "\n";
    }
}
echo "Successfully inserted $success activities.\n";
$mysqli->close();
