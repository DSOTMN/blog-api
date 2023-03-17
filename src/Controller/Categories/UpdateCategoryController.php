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
 * @OA\Put(
 *     path="/v1/blog/categories/{id}",
 *     description="Updates a category by id",
 *     tags={"Categories"},
 *     @OA\Parameter(
 *         description="Id of the cateory to update",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\RequestBody(
 *          description="Updating a single category.",
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="name", type="string", example="New category name"),
 *                  @OA\Property(property="description", type="string", example="New category description"),
 *              )
 *          )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updates single post properties."
 *     ),
 *     @OA\Response(
 *         response="400",
 *         description="Invalid input."
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Post with the given parameter not found."
 *     )
 * )
 */
class UpdateCategoryController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $repository = new CategoryRepositoryPdo($this->pdo);
        $id = $args['id'];
        $foundCategory = $repository->get($id);

        if (!$foundCategory) {
            return new JsonResponse("Category not found, try again.", 404);
        }
        if (!$data['name'] || !$data['description']) {
            return new JsonResponse("Bad input. Try again!", 400);
        }

        $repository->update($id, $data);
        $res = [
            'status' => 'success',
            'message' => 'category updated successfully',
            'data' => [
                'id' => $id
            ]
        ];

        return new JsonResponse($res, 200);
    }
}