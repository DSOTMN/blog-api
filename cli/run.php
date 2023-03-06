<?php
require_once __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ . '/../container/container.php';

/** @var PDO $pdo */
$pdo = $container->get('db');

$sql = file_get_contents(__DIR__ . '/db.sql');
$pdo->exec($sql);
echo "All done!";