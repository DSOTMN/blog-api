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

    public function __invoke(ServerRequestInterface $request, string $slug): ResponseInterface
    {
        $repository = new PostRepositoryPdo($this->pdo);
        $post = $repository->get($slug);
        $repository->delete($slug);

        $data = [
            'status' => 'successfully deleted post',
            'post-id' => $post['id']
        ];

        return new JsonResponse($data, 200);
    }
}