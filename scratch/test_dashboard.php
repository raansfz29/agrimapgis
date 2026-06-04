<?php
define('FCPATH', __DIR__ . '/public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);
require 'public/index.php';
// We won't be able to run it fully like this if index.php handles the request and dies.
