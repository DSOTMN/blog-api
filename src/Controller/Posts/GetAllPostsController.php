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

        return new JsonResponse($data, 201);
    }
}