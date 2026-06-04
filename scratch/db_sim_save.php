<?php
// Simulate posting to Activity::save
$conn = new mysqli("localhost", "root", "", "agrimapgis");

$id_lahan = 1;
$id_user = 2; // Budi Santoso
$jenis_aktivitas = "Penanaman";
$tanggal = "2026-05-17";
$deskripsi = "PPL Rajabasa mencatat penanaman padi di Sawah Blok A untuk Budi Santoso";

// Let's check if it fails database constraints
$stmt = $conn->prepare("INSERT INTO activities (id_lahan, id_user, jenis_aktivitas, tanggal, deskripsi, status) VALUES (?, ?, ?, ?, ?, ?)");
$status = "pending";
$stmt->bind_param("iissss", $id_lahan, $id_user, $jenis_aktivitas, $tanggal, $deskripsi, $status);
if ($stmt->execute()) {
    echo "Direct DB Insert Success! ID: " . $stmt->insert_id . "\n";
    // clean it up
    $conn->query("DELETE FROM activities WHERE id_aktivitas = " . $stmt->insert_id);
} else {
    echo "Direct DB Insert Failed: " . $stmt->error . "\n";
}

$conn->close();
?>
