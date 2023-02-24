<?php

namespace BlogRestApi\Blog;

class FindBlogPost
{
    public function __construct(private \PDO $connection)
    {
    }

    public function get(string $id):array
    {
        $stmt = $this->connection->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}