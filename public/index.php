<?php
include __DIR__ . '/../vendor/autoload.php';

use BlogRestApi\Controller\HomeController;
use BlogRestApi\Controller\Posts\CreateBlogPostController;
use BlogRestApi\Controller\Posts\DeleteSinglePostController;
use BlogRestApi\Controller\Posts\GetAllPostsController;
use BlogRestApi\Controller\Posts\GetSinglePostController;
use DI\Bridge\Slim\Bridge;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../container/container.php';
//AppFactory::setContainer($container);
//$app = AppFactory::create();

$app = Bridge::create($container);

$app->get('/', HomeController::class);

// Posts
$app->post('/v1/blog/posts/create', CreateBlogPostController::class);
$app->get('/v1/blog/posts', GetAllPostsController::class);
$app->get('/v1/blog/post/{id}', GetSinglePostController::class);
$app->delete('/v1/blog/post/delete/{id}', DeleteSinglePostController::class);

$app->addErrorMiddleware(true, true, true);

$app->run();