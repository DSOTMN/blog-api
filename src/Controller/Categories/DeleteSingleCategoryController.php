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
 * @OA\Delete(
 *     path="/v1/blog/categories/{id}",
 *     description="Deletes a single category by its id",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         description="Id of the category to delete",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Deletes a single category."
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Category with the given parameter not found."
 *     )
 * )
 */
class DeleteSingleCategoryController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $repository = new CategoryRepositoryPdo($this->pdo);
        $id = $args['id'];
        $foundCategory = $repository->get($id);

        if(!$foundCategory){
            return new JsonResponse([
                'status' => 'category not found',
                'id' => $id,
                'status_code' => 404
            ], 404);
        }

        $repository->delete($id);
        $res = [
            'status' => 'success',
            'category-id' => $id
        ];

        return new JsonResponse($res, 200);
    }
}