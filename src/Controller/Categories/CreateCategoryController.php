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
 * @OA\Post(
 *     path="/v1/blog/categories",
 *     description="Create a new category.",
 *     tags={"Categories"},
 *     @OA\RequestBody(
 *          description="Category to be created",
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="name", type="string", example="My category"),
 *                  @OA\Property(property="description", type="string", example="This text describes the category")
 *              )
 *          )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Category Created Successfully",
 *         @OA\MediaType(
 *             mediaType="application/json"
 *         )
 *     ),
 *     @OA\Response(
 *         response="400",
 *         description="Bad Request"
 *     )
 * )
 */

class CreateCategoryController
{
    private PDO $pdo;
    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $repository = new CategoryRepositoryPdo($this->pdo);
        $repository->store($data);

        $res = [
            'status' => 'success',
            'data' => $data
        ];

        return new JsonResponse($res, 201);
    }
}