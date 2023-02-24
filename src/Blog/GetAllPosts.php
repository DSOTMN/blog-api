<?php

namespace BlogRestApi\Blog;

use PDO;

class GetAllPosts
{
    public function __construct(private PDO $connection)
    {
    }
    public function findAll():array
    {
        $stmt = $this->connection->query('SELECT * FROM posts');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}