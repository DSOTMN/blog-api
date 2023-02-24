<?php

namespace BlogRestApi\Controller;

use BlogRestApi\Blog\FindBlogPost;
use BlogRestApi\connection;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetBlogPostController
{
    public function __invoke(ServerRequestInterface $request, array $args):ResponseInterface
    {
        $connection = connection::connect();
        $id = $args['id'];
        $findPost = new FindBlogPost($connection);
        $data = $findPost->get($id);

        return new JsonResponse($data, 201);
    }
}