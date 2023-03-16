<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Entity\Post;
use BlogRestApi\Repository\PostCategoryRepository\PostCategoryRepositoryPdo;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use Cocur\Slugify\Slugify;
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
    private PostRepositoryPdo $postRepository;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(private Container $container)
    {
        $this->pdo = $container->get('db');
        $this->fileUploadDirectory = $this->container->get('file-upload-directory');
        $this->baseUrl = $container->get('settings')['app']['domain'];
        $this->postRepository = new PostRepositoryPdo($this->pdo);
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response):ResponseInterface
    {
        $postCategoryRepo = new PostCategoryRepositoryPdo($this->pdo);
        $data = $request->getParsedBody();
        $id = uniqid('post_');

        if($this->duplicatedTitle($this->postRepository, $data['title'])){
            return new JsonResponse([
                "status" => "failed",
                "message" => "Post with the same title already exists. Try another name.",
                "status-code" => "400"
            ], 400);
        }

        // create slug out of the input title
        $slugify = new Slugify();
        $slug = $slugify->slugify($data['title']);

        // file upload
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['thumbnail'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($this->fileUploadDirectory, $uploadedFile);
            $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
        }

        $thumbnailFullPath = $this->baseUrl . 'uploads/' . $filename;

        $post = new Post(
            $id,
            $data['title'],
            $slug,
            $data['content'],
            $thumbnailFullPath,
            $data['author'],
            NULL,
            $data['categories']
        );

        $id = $this->postRepository->store($post);
        $cat = $postCategoryRepo->store($post);

        $res = [
            'status' => 'success',
            'data' => [
                'id' => $id,
                'name' => $data['title'],
                'slug' => $slug,
                'content' => $data['content'],
                'thumbnail' => $thumbnailFullPath,
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
    public function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        // see http://php.net/manual/en/function.random-bytes.php
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function duplicatedTitle(PostRepositoryPdo $postRepository, string $title): bool
    {
        $posts = $postRepository->all();
        foreach($posts as $post){
            if($post['title'] === $title){
                return true;
            }
        }
        return false;
    }
}