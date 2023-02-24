<?php
namespace BlogRestApi\Blog;

class CreateBlogPost
{
    public function __construct(private \PDO $connection){

    }

    public function create(array $inputs):string
    {
        $stmt = $this->connection->prepare('INSERT INTO posts VALUES(:id, :title, :slug, :content, :thumbnail, :author, :posted_at)');
        $id = uniqid('post_');
        $stmt->execute([
            ':id' => $id,
            ':name' => $inputs['name'],
            ':content' => $inputs['content'],
        ]);

        return $id;
    }
}