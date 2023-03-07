<?php

namespace BlogRestApi\Controller\Categories;

use BlogRestApi\Repository\CategoryRepository\CategoryRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteSingleCategoryController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }
    public function __invoke(ServerRequestInterface $request, string $id): ResponseInterface
    {
        $repository = new CategoryRepositoryPdo($this->pdo);
        $repository->delete($id);
        $res = [
            'status' => 'success',
            'category-id' => $id
        ];

        return new JsonResponse($res, 200);
    }
}