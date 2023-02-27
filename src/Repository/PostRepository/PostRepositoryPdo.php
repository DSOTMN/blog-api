<?php

namespace BlogRestApi\Repository\PostRepository;

use BlogRestApi\Entity\Post;

class PostRepositoryPdo implements PostRepository
{
    public function __construct(private readonly \PDO $connection)
    {
    }

    public function store(array $inputs): string
    {
        $stmt = $this->connection->prepare('INSERT INTO posts VALUES(:id, :title, :slug, :content, :thumbnail, :author, :posted_at)');
        $id = uniqid('post_');
        $post = new Post(
            $id,
            $inputs['name'],
            $inputs['slug'],
            $inputs['content'],
            $inputs['thumbnail'],
            $inputs['author']
        );

        $stmt->execute([
            ':id' => $post->id(),
            ':title' => $post->title(),
            ':slug' => $post->slug(),
            ':content' => $post->content(),
            ':thumbnail' => $post->thumbnail(),
            ':author' => $post->author(),
            ':posted_at' => $post->postedAt()->format('Y-m-d H:i:s')
        ]);

        return $id;
    }

    public function get($id): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM posts WHERE id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        $stmt = $this->connection->query('SELECT * FROM posts');
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function remove(string $id): string
    {
        $stmt = $this->connection->prepare('DELETE FROM posts WHERE id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return "$id deleted";
    }
}