<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Menambahkan data aktivitas test...\n";

// Tambahkan beberapa aktivitas test dengan koordinat
$sql = "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, tanggal, deskripsi, koordinat, status) VALUES
(1, 2, 'Penyemprotan Pestisida', '2026-05-08', 'Penyemprotan pestisida pada lahan padi untuk mengatasi hama wereng', 'POINT(110.123456 -7.654321)', 'approved'),
(2, 2, 'Pemupukan', '2026-05-09', 'Pemberian pupuk urea dan NPK pada tanaman jagung', 'POINT(110.223456 -7.754321)', 'pending'),
(3, 2, 'Panen', '2026-05-07', 'Panen padi varietas Ciherang dengan hasil cukup baik', 'POINT(110.323456 -7.854321)', 'approved')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Berhasil menambahkan aktivitas test\n";

    // Tampilkan data yang ditambahkan
    $result = $conn->query("SELECT id_aktivitas, jenis_aktivitas, tanggal, status FROM activities ORDER BY id_aktivitas DESC LIMIT 3");
    echo "\nAktivitas yang ditambahkan:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id_aktivitas']}, {$row['jenis_aktivitas']}, {$row['tanggal']}, {$row['status']}\n";
    }
} else {
    echo "❌ Gagal menambahkan aktivitas: " . $conn->error . "\n";
}

$conn->close();
?>