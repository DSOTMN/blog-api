<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/v1/blog/posts",
 *     description="All blog posts saved.",
 *     tags={"Blog Posts"},
 *     @OA\Response(
 *         response="200",
 *         description="Returns the list of all blog posts."
 *     )
 * )
 */
class GetAllPostsController
{
    private PDO $pdo;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(private Container $container)
    {
        $this->pdo = $this->container->get('db');
    }

    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $posts = new PostRepositoryPdo($this->pdo);
        $data = $posts->all();

        if(!$data){
            return new JsonResponse(["message" => "No posts found. Create some first.", "status_code" => 404], 404);
        }

        return new JsonResponse($data, 201);
    }
}