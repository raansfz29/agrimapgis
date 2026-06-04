<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'agrimapgis');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT id_user, email, password FROM users");
echo "Users in DB:\n";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id_user"]. " - Email: " . $row["email"]. " - Password: " . $row["password"]. "\n";
    }
} else {
    echo "0 results";
}
$mysqli->close();
