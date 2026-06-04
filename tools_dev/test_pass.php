<?php
$hash = '$2y$10$SDX9wN2tDPknFWtsf44tTuDO1koExPLt6TnG2NXBfhE1r3hrBrv1C';
$pass = 'amelia123';
if (password_verify($pass, $hash)) {
    echo "Password VERIFIED!\n";
} else {
    echo "Password WRONG!\n";
}
