<?php
require_once __DIR__ . '/../vendor/autoload.php';
use DI\Container;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$container = new Container();

$container->set('settings', static function(){
    return ['db'=>[
        'host' => 'localhost',
        'dbname' => 'blog_api',
        'user' => 'root',
        'pass' => ''
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

return $container;