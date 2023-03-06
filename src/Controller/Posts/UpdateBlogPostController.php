<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateBlogPostController
{
    private PDO $pdo;
    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke(ServerRequestInterface $request, string $id):ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $repository = new PostRepositoryPdo($this->pdo);
        $repository->update($id, $data);
        $res = [
            'status' => 'success',
            'data' => [
                "id" => $id,
                "title" => $data['title'],
	            "content" => $data['content'],
	            "thumbnail" => $data['thumbnail']
            ],
            'status-code' => 200
        ];

        return new JsonResponse($res, 200);
    }
}