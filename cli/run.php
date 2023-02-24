<?php
require_once __DIR__ . '/../vendor/autoload.php';

use BlogRestApi\connection;

$pdo = connection::connect();

$file = __DIR__ . '/db.sql';
$sql = file_get_contents($file);
$pdo->query($sql);

echo "All done!";