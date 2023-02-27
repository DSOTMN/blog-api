<?php

namespace BlogRestApi\Controller;

use BlogRestApi\Connection;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetBlogPostController
{
    public function __invoke(ServerRequestInterface $request, array $args):ResponseInterface
    {
        $connection = Connection::connect();
        $id = $args['id'];
        $findPost = new PostRepositoryPdo($connection);
        $data = $findPost->get($id);

        return new JsonResponse($data, 201);
    }
}