<?php
$conn = new mysqli("localhost", "root", "", "agrimapgis");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check lands table
$result = $conn->query("SELECT COUNT(*) as count FROM lands");
$row = $result->fetch_assoc();
echo "Total lands in database: " . $row['count'] . PHP_EOL;
$result->close();

// Check users table
$result2 = $conn->query("SELECT COUNT(*) as count FROM users");
$row2 = $result2->fetch_assoc();
echo "Total users in database: " . $row2['count'] . PHP_EOL;
$result2->close();

// Check activities table
$result3 = $conn->query("SELECT COUNT(*) as count FROM activities");
$row3 = $result3->fetch_assoc();
echo "Total activities in database: " . $row3['count'] . PHP_EOL;
$result3->close();

// Show sample lands
$result4 = $conn->query("SELECT id_lahan, nama_lahan, id_kelompok FROM lands LIMIT 5");
if ($result4->num_rows > 0) {
    echo PHP_EOL . "Sample lands:" . PHP_EOL;
    while($row = $result4->fetch_assoc()) {
        echo "  ID: " . $row['id_lahan'] . ", Name: " . $row['nama_lahan'] . ", Group: " . $row['id_kelompok'] . PHP_EOL;
    }
} else {
    echo PHP_EOL . "No lands found in database!" . PHP_EOL;
}
$result4->close();

// Show sample users
$result5 = $conn->query("SELECT id_user, nama, email, role, id_kelompok FROM users LIMIT 5");
if ($result5->num_rows > 0) {
    echo PHP_EOL . "Sample users:" . PHP_EOL;
    while($row = $result5->fetch_assoc()) {
        echo "  ID: " . $row['id_user'] . ", Name: " . $row['nama'] . ", Role: " . $row['role'] . ", Group: " . $row['id_kelompok'] . PHP_EOL;
    }
} else {
    echo PHP_EOL . "No users found in database!" . PHP_EOL;
}
$result5->close();

$conn->close();
?>