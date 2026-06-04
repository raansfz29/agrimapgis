<?php
$mysqli = new mysqli("localhost", "root", "", "agrimapgis");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

// Hapus semua aktivitas lama
$mysqli->query("DELETE FROM activities");
echo "Deleted old activities. Rows affected: " . $mysqli->affected_rows . "\n";

// Pemetaan ID:
// id_lahan 1 -> 12 (Sawah Mekar Jaya A),    id_user 101 -> 17 (Suryanto)
// id_lahan 2 -> 13 (Sawah Maju Bersama),     id_user 102 -> 18 (Wahyudi)
// id_lahan 3 -> 14 (Ladang Sukamaju Utama),  id_user 103 -> 19 (Hartono)
// id_lahan 4 -> 15 (Sawah Jaya Bersama),     id_user 104 -> 20 (Slamet Riyadi)
// id_lahan 5 -> 16 (Sawah Tani Mandiri),     id_user 105 -> 21 (Mulyono)
// id_lahan 6 -> 17 (Ladang Harapan Jaya),    id_user 106 -> 22 (Agus Santoso)
// id_lahan 7 -> 18 (Sawah Sumber Rejeki),    id_user 107 -> 23 (Supriadi)
// id_lahan 8 -> 19 (Sawah Sido Makmur),      id_user 108 -> 24 (Suminah)
// id_lahan 9 -> 20 (Sawah Karya Tani),       id_user 109 -> 25 (Bambang Eko)
// id_lahan 10 -> 21 (Sawah Tunas Harapan),   id_user 110 -> 26 (Sudirman)

$queries = [
    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (12, 17, 'Pemupukan', NULL, NULL, '2026-06-01', 'Pemupukan susulan NPK pada padi fase vegetatif.', 'pupuk_lahan1.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2295 -5.3650)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (13, 18, 'Pemanenan', 4.5, 'Ton', '2026-06-03', 'Panen jagung manis pada area fase generatif matang.', 'panen_jagung2.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2425 -5.3590)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (14, 19, 'Penanggulangan Bencana', 1.2, 'Ton', '2026-05-26', 'Evakuasi sisa tanaman padi akibat luapan banjir 30cm.', 'evakuasi_banjir.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2495 -5.3620)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (15, 20, 'Pengolahan Lahan', NULL, NULL, '2026-06-04', 'Pembajakan tanah menggunakan traktor roda dua.', 'bajak_lahan4.jpg', 'pending', NOW(), ST_GeomFromText('POINT(105.2390 -5.3530)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (16, 21, 'Penyemprotan Hama', NULL, NULL, '2026-06-02', 'Penyemprotan pestisida darurat serangan wereng cokelat.', 'semprot_wereng.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2460 -5.3680)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (17, 22, 'Pemeliharaan', NULL, NULL, '2026-06-02', 'Pengairan berkala pada lahan jagung fase generatif.', NULL, 'approved', NOW(), ST_GeomFromText('POINT(105.2402 -5.3531)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (18, 23, 'Pemanenan', 3.9, 'Ton', '2026-05-29', 'Pembersihan jerami sisa panen raya padi minggu lalu.', 'pasca_panen7.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2471 -5.3682)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (19, 24, 'Pemupukan', NULL, NULL, '2026-06-03', 'Pemberian pupuk dasar organik pada padi vegetatif awal.', 'pupuk_organik8.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2221 -5.3601)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (20, 25, 'Penyemprotan Hama', NULL, NULL, '2026-06-01', 'Penyemprotan fungisida pencegahan jamur tanaman padi.', 'semprot_fungi9.jpg', 'approved', NOW(), ST_GeomFromText('POINT(105.2452 -5.3711)'))",

    "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, hasil_panen, satuan, tanggal, deskripsi, foto, status, created_at, koordinat) VALUES 
    (21, 26, 'Pemanenan', 5.0, 'Ton', '2026-06-04', 'Proses pemotongan padi menggunakan mesin combine harvester.', 'combine_harvester10.jpg', 'pending', NOW(), ST_GeomFromText('POINT(105.2385 -5.3641)'))"
];

$success = 0;
foreach($queries as $index => $q) {
    if ($mysqli->query($q)) {
        $success++;
        echo "OK: Row " . ($index + 1) . " inserted.\n";
    } else {
        echo "ERROR Row " . ($index + 1) . ": " . $mysqli->error . "\n";
    }
}
echo "\nTotal: $success/10 activities inserted.\n";
$mysqli->close();
