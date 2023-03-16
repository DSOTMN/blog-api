<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetSinglePostController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request, string $slug):ResponseInterface
    {
        $findPost = new PostRepositoryPdo($this->pdo);
        $data = $findPost->get($slug);

        if(!$data){
            return new JsonResponse(["message" => "Post not found. Try something else.", "status_code" => 404], 404);
        }

        return new JsonResponse($data, 201);
    }
}