<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/v1/blog/posts/{slug}",
 *     description="Returns a single blog post by its slug",
 *     tags={"Blog Posts"},
 *     @OA\Parameter(
 *         description="Slug of the post to return",
 *         in="path",
 *         name="slug",
 *         example="post-slug",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Returns the single post."
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Post with the given parameter not found."
 *     )
 * )
 */

class GetSinglePostController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args):ResponseInterface
    {
        $findPost = new PostRepositoryPdo($this->pdo);
        $data = $findPost->get($args['slug']);

        if(!$data){
            return new JsonResponse(["message" => "Post not found. Try something else.", "status_code" => 404], 404);
        }

        return new JsonResponse($data, 201);
    }
}