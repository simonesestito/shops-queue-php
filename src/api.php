<?php
require_once __DIR__ . '/utils.php';
require_all();

echo $_SERVER['PATH_INFO'];
echo "\n";
echo Validator::class;