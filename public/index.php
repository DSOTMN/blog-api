<?php
use BlogRestApi\Controller\Categories\CreateCategoryController;
use BlogRestApi\Controller\Categories\DeleteSingleCategoryController;
use BlogRestApi\Controller\Categories\GetAllCategoriesController;
use BlogRestApi\Controller\Categories\GetSingleCategoryController;
use BlogRestApi\Controller\Categories\UpdateCategoryController;
use BlogRestApi\Controller\HomeController;
use BlogRestApi\Controller\OpenApiController;
use BlogRestApi\Controller\Posts\CreateBlogPostController;
use BlogRestApi\Controller\Posts\DeleteSinglePostController;
use BlogRestApi\Controller\Posts\GetAllPostsController;
use BlogRestApi\Controller\Posts\GetSinglePostController;
use BlogRestApi\Controller\Posts\UpdateBlogPostController;
use Laminas\Diactoros\Response\HtmlResponse;
use Slim\Factory\AppFactory;

require __DIR__ . '/../boot.php';

$container = require __DIR__ . '/../container/container.php';

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/', HomeController::class);
$app->get('/openapi', OpenApiController::class);
$app->get('/apidocs', fn () => new HtmlResponse(file_get_contents(__DIR__ . '/api-docs/index.php')));

// Posts
$app->post('/v1/blog/posts', CreateBlogPostController::class);
$app->get('/v1/blog/posts', GetAllPostsController::class);
$app->get('/v1/blog/posts/{slug}', GetSinglePostController::class);
$app->delete('/v1/blog/posts/{slug}', DeleteSinglePostController::class);
$app->post('/v1/blog/posts/update/{slug}', UpdateBlogPostController::class);

// Categories
$app->post('/v1/blog/categories', CreateCategoryController::class);
$app->get('/v1/blog/categories', GetAllCategoriesController::class);
$app->get('/v1/blog/categories/{id}', GetSingleCategoryController::class);
$app->delete('/v1/blog/categories/{id}', DeleteSingleCategoryController::class);
$app->put('/v1/blog/categories/{id}', UpdateCategoryController::class);

$app->addErrorMiddleware(true, true, true);

$app->run();