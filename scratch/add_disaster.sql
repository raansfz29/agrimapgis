INSERT INTO lands (id_kelompok, id_user, nama_lahan, komoditas, alamat, status_fase, status_bencana, deskripsi_bencana, tanggal_bencana, geom, luas) 
VALUES (
    (SELECT id_kelompok FROM users WHERE role = 'petani' LIMIT 1),
    (SELECT id_user FROM users WHERE role = 'petani' LIMIT 1),
    'Sawah Blok C - Terkena Banjir',
    'padi',
    'Jl. Rajabasa Raya, Samping Sungai',
    'panen',
    'darurat',
    'Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.',
    NOW(),
    ST_GeomFromGeoJSON('{"type": "Polygon", "coordinates": [[[105.245, -5.375], [105.250, -5.375], [105.250, -5.380], [105.245, -5.380], [105.245, -5.375]]]}', 2, 4326),
    1.45
);
