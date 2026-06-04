<?php
$mysqli = new mysqli("localhost", "root", "", "agrimapgis");
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

$queries = [
    // 1. Mekar Jaya (id_kelompok=9, id_user=17)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (9, 17, 'Sawah Mekar Jaya A', 'Padi', 'Jl. Kapten Abdul Haq, Rajabasa', ST_GeomFromText('POLYGON((105.234 -5.364, 105.234 -5.366, 105.236 -5.366, 105.236 -5.364, 105.234 -5.364))'), 22.5, -5.364200, 105.234500, 'pemeliharaan', 'normal', NULL, NULL, NULL, NOW())",

    // 2. Maju Bersama (id_kelompok=10, id_user=18)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (10, 18, 'Sawah Maju Bersama', 'Jagung', 'Jl. Komarudin, Rajabasa', ST_GeomFromText('POLYGON((105.241 -5.358, 105.241 -5.360, 105.243 -5.360, 105.243 -5.358, 105.241 -5.358))'), 18.2, -5.358900, 105.241200, 'pemeliharaan', 'normal', NULL, NULL, NULL, NOW())",

    // 3. Sukamaju I (id_kelompok=11, id_user=19)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (11, 19, 'Ladang Sukamaju Utama', 'Padi', 'Rajabasa Jaya', ST_GeomFromText('POLYGON((105.250 -5.361, 105.250 -5.364, 105.253 -5.364, 105.253 -5.361, 105.250 -5.361))'), 31.4, -5.361100, 105.250400, 'panen', 'darurat', 'banjir_lahan3.jpg', 'Tergenang banjir luapan sungai setinggi 30cm', '2026-05-25', NOW())",

    // 4. Jaya Bersama (id_kelompok=12, id_user=20)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (12, 20, 'Sawah Jaya Bersama', 'Jagung', 'Rajabasa Raya', ST_GeomFromText('POLYGON((105.228 -5.371, 105.228 -5.373, 105.230 -5.373, 105.230 -5.371, 105.228 -5.371))'), 15.8, -5.371000, 105.228900, 'persiapan', 'normal', NULL, NULL, NULL, NOW())",

    // 5. Tani Mandiri (id_kelompok=13, id_user=21)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (13, 21, 'Sawah Tani Mandiri', 'Padi', 'Gedong Meneng, Rajabasa', ST_GeomFromText('POLYGON((105.233 -5.375, 105.233 -5.378, 105.235 -5.378, 105.235 -5.375, 105.233 -5.375))'), 26.3, -5.375600, 105.233100, 'pemeliharaan', 'darurat', 'hama_lahan5.jpg', 'Terserang hama wereng cokelat skala ringan', '2026-06-01', NOW())",

    // 6. Harapan Jaya (id_kelompok=14, id_user=22)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (14, 22, 'Ladang Harapan Jaya', 'Jagung', 'Jl. Terusan Haji Mena, Rajabasa', ST_GeomFromText('POLYGON((105.239 -5.352, 105.239 -5.354, 105.241 -5.354, 105.241 -5.352, 105.239 -5.352))'), 20.1, -5.352400, 105.239800, 'pemeliharaan', 'normal', NULL, NULL, NULL, NOW())",

    // 7. Sumber Rejeki (id_kelompok=15, id_user=23)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (15, 23, 'Sawah Sumber Rejeki', 'Padi', 'Rajabasa Permai', ST_GeomFromText('POLYGON((105.246 -5.367, 105.246 -5.369, 105.248 -5.369, 105.248 -5.367, 105.246 -5.367))'), 19.5, -5.367800, 105.246500, 'bera', 'normal', NULL, NULL, NULL, NOW())",

    // 8. Sido Makmur (id_kelompok=16, id_user=24)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (16, 24, 'Sawah Sido Makmur', 'Padi', 'Jl. Raden Gunawan, Rajabasa', ST_GeomFromText('POLYGON((105.221 -5.359, 105.221 -5.361, 105.223 -5.361, 105.223 -5.359, 105.221 -5.359))'), 24.6, -5.359500, 105.221200, 'pemeliharaan', 'normal', NULL, NULL, NULL, NOW())",

    // 9. Karya Tani (id_kelompok=17, id_user=25)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (17, 25, 'Sawah Karya Tani', 'Padi', 'Rajabasa Barat', ST_GeomFromText('POLYGON((105.244 -5.370, 105.244 -5.372, 105.246 -5.372, 105.246 -5.370, 105.244 -5.370))'), 17.8, -5.370200, 105.244100, 'pemeliharaan', 'normal', NULL, NULL, NULL, NOW())",

    // 10. Tunas Harapan (id_kelompok=18, id_user=26)
    "INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, geom, luas, latitude, longitude, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana, created_at) VALUES 
    (18, 26, 'Sawah Tunas Harapan', 'Padi', 'Jl. Zainal Abidin Pagar Alam, Rajabasa', ST_GeomFromText('POLYGON((105.237 -5.363, 105.237 -5.365, 105.239 -5.365, 105.239 -5.363, 105.237 -5.363))'), 22.8, -5.363300, 105.237800, 'panen', 'normal', NULL, NULL, NULL, NOW())"
];

$success = 0;
foreach($queries as $index => $q) {
    if ($mysqli->query($q)) {
        $success++;
    } else {
        echo "Error on row " . ($index + 1) . ": " . $mysqli->error . "\n";
    }
}
echo "Successfully inserted $success lands.\n";
$mysqli->close();
