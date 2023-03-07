<?php

namespace BlogRestApi\Controller\Categories;

use BlogRestApi\Repository\CategoryRepository\CategoryRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateCategoryController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request, string $id): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $repository = new CategoryRepositoryPdo($this->pdo);
        $repository->update($id, $data);
        $res = [
            'status' => 'success',
            'message' => 'category updated successfully',
            'data' => [
                'id' => $id
            ]
        ];

        return new JsonResponse($res, 201);
    }
}