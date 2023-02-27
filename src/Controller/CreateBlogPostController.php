<?php

namespace BlogRestApi\Controller;

use BlogRestApi\Connection;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateBlogPostController
{
    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $connection = Connection::connect();
        $newPost = new PostRepositoryPdo($connection);
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