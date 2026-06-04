<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add id_user to lands
$sql = "ALTER TABLE lands ADD COLUMN id_user INT AFTER id_kelompok";
if ($conn->query($sql) === TRUE) {
    echo "Column id_user added to lands successfully\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}

// Update existing lands to belong to the first petani found (for demo consistency)
$res = $conn->query("SELECT id_user FROM users WHERE role = 'petani' LIMIT 1");
if ($res && $res->num_rows > 0) {
    $petaniId = $res->fetch_assoc()['id_user'];
    $conn->query("UPDATE lands SET id_user = $petaniId");
    echo "Existing lands updated to owner ID $petaniId\n";
}

$conn->close();
