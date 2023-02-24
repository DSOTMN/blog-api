<?php

namespace BlogRestApi\Controller;

use BlogRestApi\Blog\GetAllPosts;
use BlogRestApi\connection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAllPostsController
{
    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $connection = connection::connect();
        $posts = new GetAllPosts($connection);
        $data = $posts->findAll();

        return new JsonResponse($data, 201);
    }
}