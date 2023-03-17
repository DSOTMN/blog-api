<?php

namespace BlogRestApi\Controller\Categories;

use BlogRestApi\Repository\CategoryRepository\CategoryRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/v1/blog/categories/{id}",
 *     description="Returns a single category by its id",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         description="Category id",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns the single category."
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Category with the given parameter not found."
 *     )
 * )
 */
class GetSingleCategoryController
{
    private PDO $pdo;
    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args):ResponseInterface
    {
        $repository = new CategoryRepositoryPdo($this->pdo);
        $id = $args['id'];
        $category = $repository->get($id);

        if(!$category){
            return new JsonResponse(
                [
                'status' => 'category not found',
                'data' => $category
                ], 404
            );
        }

        $res = [
            'status' => 'success',
            'data' => $category
        ];

        return new JsonResponse($res, 200);
    }
}