<?php

namespace BlogRestApi\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Slim\Psr7\Response;
use Slim\Psr7\Request;

class HomeController
{
    public function __invoke(Request $request, Response $response): JsonResponse
    {
        $data = [
            'app' => 'blog-posts-rest-api',
            'version' => '1.0'
        ];

        return new JsonResponse($data);
    }
}