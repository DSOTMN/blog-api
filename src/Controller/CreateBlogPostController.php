<?php

namespace BlogRestApi\Controller;

use BlogRestApi\Blog\CreateBlogPost;
use BlogRestApi\connection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateBlogPostController
{
    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $connection = connection::connect();
        $newPost = new CreateBlogPost($connection);
        $data = json_decode($request->getBody()->getContents(), true);
        $id = $newPost->create($data);

        $res = [
            'status' => 'success',
            'data' => [
                'id' => $id
            ]
        ];

        return new JsonResponse($res, 201);
    }
}