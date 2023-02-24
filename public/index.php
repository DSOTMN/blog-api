<?php
include __DIR__ . '/../vendor/autoload.php';

use BlogRestApi\Controller\CreateBlogPostController;
use BlogRestApi\Controller\GetAllPostsController;
use BlogRestApi\Controller\GetBlogPostController;
use League\Route\Router;
use Laminas\Diactoros\ServerRequestFactory;


$request = ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$router = new Router;
// get single i get all posts možeš unutar jedne klase staviti
//create post
$router->post('/v1/blog/post/create', CreateBlogPostController::class);
//get post
$router->get('/v1/blog/post/{id}', GetBlogPostController::class);
//get all posts
$router->get('/v1/blog/posts', GetAllPostsController::class);

$response = $router->dispatch($request);

// send the response to the browser
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);