<?php

namespace BlogRestApi\Repository\PostCategoryRepository;

use BlogRestApi\Entity\Post;

class PostCategoryRepositoryPdo implements PostCategoryRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function store(Post $post): mixed
    {
        $stmt = $this->pdo->prepare('INSERT INTO posts_categories VALUES(:id_post, :id_category)');

        foreach ($post->categories() as $category){
            $stmt->execute([
                ':id_post' => $post->id(),
                ':id_category' => $category
            ]);
        }

        return $post->categories();
    }
}