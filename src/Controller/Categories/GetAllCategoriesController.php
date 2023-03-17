<?php

namespace BlogRestApi\Controller\Categories;

use BlogRestApi\Repository\CategoryRepository\CategoryRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @OA\Get(
 *     path="/v1/blog/categories",
 *     description="All categories saved.",
 *     tags={"Categories"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns the list of all categories."
 *     )
 * )
 */

class GetAllCategoriesController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $repository = new CategoryRepositoryPdo($this->pdo);
        $categories = $repository->all();
        return new JsonResponse($categories, 200);
    }
}