<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/v1/blog/posts/update/{slug}",
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
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(property="title", type="string", example="My New Blog Post"),
 *                  @OA\Property(property="content", type="string", example="Lorem Ipsum Dolorem"),
 *                  @OA\Property(property="thumbnail", type="string"),
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

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
        $this->fileUploadDirectory = $container->get('file-upload-directory');
        $this->baseUrl = $container->get('settings')['app']['domain'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args):ResponseInterface
    {
        $data = $request->getParsedBody();
        $repository = new PostRepositoryPdo($this->pdo);
        $slug = $args['slug'];

        $post = $repository->get($slug);
        $uploadedFiles = $request->getUploadedFiles();

        if (!$post) {
            return new JsonResponse("Post not found, try again.", 404);
        }
        if (!$data['title'] || !$data['content'] || !$uploadedFiles) {
            return new JsonResponse("Bad input. Try again!", 400);
        }

        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['thumbnail'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($this->fileUploadDirectory, $uploadedFile);
            $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
        }

        $data['thumbnail'] = $this->baseUrl . 'uploads/' . $filename;

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

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory The directory to which the file is moved
     * @param UploadedFileInterface $uploadedFile The file uploaded file to move
     *
     * @return string The filename of moved file
     */
    private function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        // see http://php.net/manual/en/function.random-bytes.php
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}