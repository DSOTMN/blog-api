<?php

namespace BlogRestApi\Controller;

use BlogRestApi\Connection;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAllPostsController
{
    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $connection = Connection::connect();
        $posts = new PostRepositoryPdo($connection);
        $data = $posts->all();

        return new JsonResponse($data, 201);
    }
}