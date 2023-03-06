<?php

namespace BlogRestApi\Controller\Posts;

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
        $newPost = new PostRepositoryPdo($this->pdo);
        $data = json_decode($request->getBody()->getContents(), true);
        $id = $newPost->store($data);

        $res = [
            'status' => 'success',
            'data' => [
                'id' => $id
            ]
        ];

        return new JsonResponse($res, 201);
    }
}