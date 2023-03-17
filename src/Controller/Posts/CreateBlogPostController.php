<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Entity\Post;
use BlogRestApi\Repository\CategoryRepository\CategoryRepositoryPdo;
use BlogRestApi\Repository\PostCategoryRepository\PostCategoryRepositoryPdo;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use Cocur\Slugify\Slugify;
use DI\DependencyException;
use DI\NotFoundException;
use JsonException;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;
use Psr\Http\Message\UploadedFileInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/v1/blog/posts",
 *     description="Create a new blog post.",
 *     tags={"Blog Posts"},
 *     @OA\RequestBody(
 *          description="Post to be created",
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(property="title", type="string", example="My New Blog Post"),
 *                  @OA\Property(property="content", type="string", example="Lorem Ipsum Dolorem"),
 *                  @OA\Property(property="thumbnail", type="string"),
 *                  @OA\Property(property="author", type="string", example="My Name"),
 *                  @OA\Property(property="categories", type="array",
 *                         @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="string", format="uniqid")
 *                     )
 *                   ),
 *              )
 *          )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Post Created Successfully",
 *         @OA\MediaType(
 *             mediaType="application/json"
 *         )
 *     ),
 *     @OA\Response(
 *         response="400",
 *         description="Bad Request"
 *     )
 * )
 */
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
        $categoryRepo = new CategoryRepositoryPdo($this->pdo);
        $data = $request->getParsedBody();
        $foundCategory = $categoryRepo->get($data['categories']);
        if(!$foundCategory){
            return new JsonResponse([
                "status" => "failed",
                "message" => "Category does not exist. Try something else.",
                "status-code" => 404
            ], 404);
        }


        if($this->duplicatedTitle($this->postRepository, $data['title'])){
            return new JsonResponse([
                "status" => "failed",
                "message" => "Post with the same title already exists. Try another name.",
                "status-code" => "400"
            ], 400);
        }

        // file upload
        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['thumbnail'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($this->fileUploadDirectory, $uploadedFile);
            $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
        }

        $thumbnailFullPath = $this->baseUrl . 'uploads/' . $filename;
        $id = uniqid('post_');
        $post = new Post(
            $id,
            $data['title'],
            $this->slugify($data['title']),
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
                'slug' => $post->slug(),
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

    public function slugify(string $title): string
    {
        // create slug out of the input title
        $slugify = new Slugify();
        return $slugify->slugify($title);
    }
}