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
 * @OA\Delete(
 *     path="/v1/blog/posts/{slug}",
 *     description="Deletes a single blog post by its slug",
 *     tags={"Blog Posts"},
 *     @OA\Parameter(
 *         description="Slug of the post to delete",
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
 *         description="Deletes a single post."
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Post with the given parameter not found."
 *     )
 * )
 */
class DeleteSinglePostController
{
    private PDO $pdo;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $repository = new PostRepositoryPdo($this->pdo);
        $slug = $args['slug'];
        $post = $repository->get($slug);

        if(!$post){
            return new JsonResponse(
                $data = [
                'status' => 'post not found',
                'status_code' => 404
                ], 404
            );
        }

        $repository->delete($slug);

        $data = [
            'status' => 'successfully deleted post',
            'post-id' => $post['id']
        ];

        return new JsonResponse($data, 200);
    }
}