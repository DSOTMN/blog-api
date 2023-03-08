<?php

namespace BlogRestApi\Repository\PostRepository;

use BlogRestApi\Entity\Post;

class PostRepositoryPdo implements PostRepository
{
    public function __construct(private readonly \PDO $connection)
    {
    }

    public function store(Post $post): string
    {
        $stmt = $this->connection->prepare('INSERT INTO posts VALUES(:id, :title, :slug, :content, :thumbnail, :author, :posted_at)');

        $stmt->execute([
            ':id' => $post->id(),
            ':title' => $post->title(),
            ':slug' => $post->slug(),
            ':content' => $post->content(),
            ':thumbnail' => $post->thumbnail(),
            ':author' => $post->author(),
            ':posted_at' => $post->postedAt()->format('Y-m-d H:i:s')
        ]);

        return $post->id();
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
        //$stmt = $this->connection->query('SELECT * FROM posts');
        $stmt = $this->connection->query(<<<SQL
        SELECT posts.*, posts_categories.* FROM posts_categories
        JOIN categories
        ON posts_categories.id_category = categories.id
        JOIN posts
        ON posts_categories.id_post = posts.id                 
        SQL);

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

    public function update(string $id, array $data): string
    {
        $stmt = $this->connection->prepare('UPDATE posts SET title=:title, content=:content, thumbnail=:thumbnail WHERE id=:id');

        $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':thumbnail' => $data['thumbnail'],
            ':id' => $id,
        ]);

        return "Post: successfully updated";
    }
}