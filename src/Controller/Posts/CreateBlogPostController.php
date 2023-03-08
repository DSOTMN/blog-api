<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Entity\Post;
use BlogRestApi\Repository\PostCategoryRepository\PostCategoryRepositoryPdo;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\DependencyException;
use DI\NotFoundException;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;

class CreateBlogPostController
{
    private PDO $pdo;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(private Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $postRepo = new PostRepositoryPdo($this->pdo);
        $postCategoryRepo = new PostCategoryRepositoryPdo($this->pdo);
        $data = json_decode($request->getBody()->getContents(), true);
        $id = uniqid('post_');
        $post = new Post(
            $id,
            $data['name'],
            $data['slug'],
            $data['content'],
            $data['thumbnail'],
            $data['author'],
            NULL,
            $data['category']
        );

        $id = $postRepo->store($post);
        $cat = $postCategoryRepo->store($post);

        $res = [
            'status' => 'success',
            'data' => [
                'id' => $id
            ]
        ];

        // NAPRAVI RESPONSE DA JE LIJEP I PRIKAZUJE SVE PODATKE!

        return new JsonResponse($res, 201);
    }
}