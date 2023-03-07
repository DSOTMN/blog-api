<?php

namespace BlogRestApi\Controller\Categories;

use BlogRestApi\Repository\CategoryRepository\CategoryRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetSingleCategoryController
{
    private PDO $pdo;
    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }
    public function __invoke(ServerRequestInterface $request, string $id):ResponseInterface
    {
        $repository = new CategoryRepositoryPdo($this->pdo);
        $category = $repository->get($id);
        $res = [
            'status' => 'success',
            'data' => $category
        ];

        return new JsonResponse($res, 200);
    }
}