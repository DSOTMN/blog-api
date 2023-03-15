<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Entity\Post;
use BlogRestApi\Repository\PostCategoryRepository\PostCategoryRepositoryPdo;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use DI\DependencyException;
use DI\NotFoundException;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;
use Psr\Http\Message\UploadedFileInterface;

class CreateBlogPostController
{
    private PDO $pdo;
    private string $fileUploadDirectory;
    private string $baseUrl;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(private Container $container)
    {
        $this->pdo = $container->get('db');
        $this->fileUploadDirectory = $this->container->get('file-upload-directory');
        $this->baseUrl = $container->get('settings')['app']['domain'];
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response):ResponseInterface
    {
        $postRepo = new PostRepositoryPdo($this->pdo);
        $postCategoryRepo = new PostCategoryRepositoryPdo($this->pdo);
        //$data = $request->getBody()->getContents();
        $data = $request->getParsedBody();
        $id = uniqid('post_');

        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['thumbnail'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($this->fileUploadDirectory, $uploadedFile);
            $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
        }

        $thumbFilePath = $this->baseUrl . 'uploads/' . $filename;

        $post = new Post(
            $id,
            $data['name'],
            $data['slug'],
            $data['content'],
            $thumbFilePath,
            $data['author'],
            NULL,
            $data['categories']
        );

        $id = $postRepo->store($post);
        $cat = $postCategoryRepo->store($post);

        $res = [
            'status' => 'success',
            'data' => [
                'id' => $id,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'content' => $data['content'],
                'thumbnail' => $thumbFilePath,
                'author' => $data['author'],
                'posted_at' => $post->postedAt()->format('Y-m-d H:i:s'),
                'categories' => $cat
            ],
            'status_code' => 201
        ];
        return new JsonResponse($res, 201);
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
    function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        // see http://php.net/manual/en/function.random-bytes.php
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}