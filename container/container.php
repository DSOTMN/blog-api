<?php
require_once __DIR__ . '/../vendor/autoload.php';
use DI\Container;

$container = new Container();

$container->set('settings', static function(){
    return [
        'app' => [
            'domain' => $_ENV['APP_URL'] ?? 'localhost'
        ],
        'db' => [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'dbname' => $_ENV['DB_NAME'] ?? 'blog_posts_api',
            'user' => $_ENV['DB_USER'] ?? 'root',
            'pass' => $_ENV['DB_PASS'] ?? ''
        ]
    ];
});

$container->set('db', static function ($c) {
    $db = $c->get('settings')['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
});

$container->set('file-upload-directory', __DIR__ . '/../public/uploads/');

return $container;