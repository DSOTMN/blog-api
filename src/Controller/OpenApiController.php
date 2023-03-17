<?php

namespace BlogRestApi\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use OpenApi\Generator;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Blog Rest API", version="1.0")
 */
class OpenApiController
{
    public function __invoke(Request $request, Response $response, $args): JsonResponse
    {
        $openapi = Generator::scan([__DIR__ . '/../../src']);
        return new JsonResponse($openapi);
    }
}