<?php

namespace BlogRestApi\Controller\Posts;

use BlogRestApi\Entity\Post;
use BlogRestApi\Repository\CategoryRepository\CategoryRepositoryPdo;
use BlogRestApi\Repository\PostCategoryRepository\PostCategoryRepositoryPdo;
use BlogRestApi\Repository\PostRepository\PostRepositoryPdo;
use Cocur\Slugify\Slugify;
use DI\DependencyException;
use DI\NotFoundException;
use http\Env\Response;
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
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="title", type="string", example="My New Blog Post"),
 *                  @OA\Property(property="content", type="string", example="Lorem Ipsum Dolorem"),
 *                  @OA\Property(property="thumbnail", type="string", example="C:/Users/User/image.jpg"),
 *                  @OA\Property(property="author", type="string", example="My Name"),
 *                  @OA\Property(property="categories", type="array",
 *                         @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="category_id", type="string", example="category_123, category_456")
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
        $data = json_decode($request->getBody()->getContents(), true);


        $categories = explode(", ", $data['categories']);
        foreach ($categories as $category){
            if(!isset($categoryRepo->get($category)['category_id'])){
                return new JsonResponse([
                    "status" => "failed",
                    "message" => "bad category input",
                    "status-code" => "400"
                ], 400);
            }
        }

        if($this->duplicatedTitle($this->postRepository, $data['title'])){
            return new JsonResponse([
                "status" => "failed",
                "message" => "Post with the same title already exists. Try another name.",
                "status-code" => "400"
            ], 400);
        }
        $thumbnail = file_get_contents($data['thumbnail']);
        $ext = substr(strrchr($data['thumbnail'], '.'), 0);
        $fileName = uniqid('image_', false) . $ext;


        $b64 = base64_encode($thumbnail);

        file_put_contents($this->fileUploadDirectory . $fileName, base64_decode($b64));
        $thumbnailFullPath = $this->baseUrl . 'uploads/' . $fileName;

        $id = uniqid('post_');
        $post = new Post(
            $id,
            $data['title'],
            $this->slugify($data['title']),
            $data['content'],
            $thumbnailFullPath,
            $data['author'],
            NULL,
            $categories
        );

        $id = $this->postRepository->store($post);
        $postCategoryRepo->store($post);

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
                'categories' => $categories
            ],
            'status_code' => 201
        ];
        return new JsonResponse($res, 201);
    }
    private function duplicatedTitle(PostRepositoryPdo $postRepository, string $title): bool
    {
        $posts = $postRepository->all();
        foreach($posts as $post){
            if($post['title'] === $title){
                return true;
            }
        }
        return false;
    }

    private function slugify(string $title): string
    {
        // create slug out of the input title
        $slugify = new Slugify();
        return $slugify->slugify($title);
    }

}