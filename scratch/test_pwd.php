<?php
$hash_budi = '$2y$10$DPt5bhi.GgWLTyINWaYKY.aCnDqHhSiNgxxgu7Ak5vlFvDDK2Hfya';
$hash_ppl = '$2y$10$kotcU7YNrWjhLQvz2QH4yOwb1e1.6zSDnIAc6Sm96jfAnY0CdGgBS';

echo "Budi (petani123): " . (password_verify('petani123', $hash_budi) ? 'VALID' : 'INVALID') . "\n";
echo "PPL (ppl123): " . (password_verify('ppl123', $hash_ppl) ? 'VALID' : 'INVALID') . "\n";
