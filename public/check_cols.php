<?php
$db = new mysqli('localhost', 'root', '', 'agrimapgis');
$res = $db->query('DESCRIBE lands');
while($row = $res->fetch_assoc()) echo $row['Field'] . " | ";
