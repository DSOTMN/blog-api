<?php

namespace BlogRestApi\Repository\PostCategoryRepository;

use BlogRestApi\Entity\Post;
use BlogRestApi\Entity\Category;

class PostCategoryRepositoryPdo implements PostCategoryRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function store(Post $post): string
    {
        $stmt = $this->pdo->prepare('INSERT INTO posts_categories VALUES(:id_post, :id_category)');

        $stmt->execute([
            ':id_post' => $post->id(),
            ':id_category' => $post->categories()
        ]);
        return $post->categories();
    }
}