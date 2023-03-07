<?php
include __DIR__ . '/../vendor/autoload.php';

use BlogRestApi\Controller\Categories\CreateCategoryController;
use BlogRestApi\Controller\Categories\DeleteSingleCategoryController;
use BlogRestApi\Controller\Categories\GetAllCategoriesController;
use BlogRestApi\Controller\Categories\GetSingleCategoryController;
use BlogRestApi\Controller\Categories\UpdateCategoryController;
use BlogRestApi\Controller\HomeController;
use BlogRestApi\Controller\Posts\CreateBlogPostController;
use BlogRestApi\Controller\Posts\DeleteSinglePostController;
use BlogRestApi\Controller\Posts\GetAllPostsController;
use BlogRestApi\Controller\Posts\GetSinglePostController;
use BlogRestApi\Controller\Posts\UpdateBlogPostController;
use DI\Bridge\Slim\Bridge;

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
$app->put('/v1/blog/post/update/{id}', UpdateBlogPostController::class);

// Categories
$app->post('/v1/blog/categories/create', CreateCategoryController::class);
$app->get('/v1/blog/categories/', GetAllCategoriesController::class);
$app->get('/v1/blog/categories/{id}', GetSingleCategoryController::class);
$app->delete('/v1/blog/categories/delete/{id}', DeleteSingleCategoryController::class);
$app->put('/v1/blog/categories/update/{id}', UpdateCategoryController::class);

$app->addErrorMiddleware(true, true, true);

$app->run();