<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/v1/blog/posts/{slug}",
 *     description="Updates a single post by its slug",
 *     tags={"Blog Posts"},
 *     @OA\Parameter(
 *         description="Slug of the post to update",
 *         in="path",
 *         name="slug",
 *         example="post-slug",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\RequestBody(
 *          description="Updating a single post.",
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="title", type="string", example="My New Blog Post"),
 *                  @OA\Property(property="content", type="string", example="Lorem Ipsum Dolorem"),
 *                  @OA\Property(property="thumbnail", type="string", example="C:/Users/User/image-2.jpg"),
 *              )
 *          )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updates single post properties."
 *     ),
 *     @OA\Response(
 *         response="400",
 *         description="Invalid input."
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Post with the given parameter not found."
 *     )
 * )
 */

class UpdateBlogPostController
{
    private PDO $pdo;
    private string $fileUploadDirectory;
    private string $baseUrl;

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
        $this->fileUploadDirectory = $container->get('file-upload-directory');
        $this->baseUrl = $container->get('settings')['app']['domain'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args):ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $repository = new PostRepositoryPdo($this->pdo);
        $slug = $args['slug'];

        $post = $repository->get($slug);

        if (!$post) {
            return new JsonResponse("Post not found, try again.", 404);
        }
        if (!$data['title'] || !$data['content'] || !$data['thumbnail']) {
            return new JsonResponse("Bad input. Try again!", 400);
        }

        $thumbnail = file_get_contents($data['thumbnail']);
        $ext = substr(strrchr($data['thumbnail'], '.'), 0);
        $fileName = uniqid('image_', false) . $ext;
        $b64 = base64_encode($thumbnail);
        file_put_contents($this->fileUploadDirectory . $fileName, base64_decode($b64));
        $data['thumbnail'] = $this->baseUrl . 'uploads/' . $fileName;

        $repository->update($slug, $data);
        $res = [
            'status' => 'success',
            'data' => [
                "id" => $post['id'],
                "title" => $data['title'],
	            "content" => $data['content'],
	            "thumbnail" => $data['thumbnail']
            ],
            'status-code' => 200
        ];

        return new JsonResponse($res, 200);
    }
}