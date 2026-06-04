<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Menambahkan aktivitas pending untuk testing approval...\n";

// Tambahkan aktivitas dengan status pending
$sql = "INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, tanggal, deskripsi, koordinat, status) VALUES
(1, 2, 'Pengairan Lahan', '2026-05-09', 'Melakukan pengairan pada lahan padi untuk menjaga kelembaban tanah', 'POINT(110.123456 -7.654321)', 'pending'),
(2, 2, 'Pembersihan Gulma', '2026-05-10', 'Membersihkan gulma yang tumbuh di sekitar tanaman jagung', 'POINT(110.223456 -7.754321)', 'pending')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Berhasil menambahkan aktivitas pending\n";

    // Tampilkan aktivitas pending
    $result = $conn->query("SELECT id_aktivitas, jenis_aktivitas, tanggal, status FROM activities WHERE status = 'pending'");
    echo "\nAktivitas pending yang ditambahkan:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id_aktivitas']}, {$row['jenis_aktivitas']}, {$row['tanggal']}, {$row['status']}\n";
    }
} else {
    echo "❌ Gagal menambahkan aktivitas: " . $conn->error . "\n";
}

$conn->close();
?>