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
        $stmt = $this->connection->prepare(<<<SQL
            INSERT INTO posts 
            VALUES(:id, :title, :slug, :content, :thumbnail, :author, :posted_at)
            SQL);

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
        $stmt = $this->connection->prepare(<<<SQL
            SELECT posts.*, categories.*
            FROM posts_categories
            JOIN categories
            ON posts_categories.id_category = categories.category_id
            LEFT JOIN posts
            ON posts_categories.id_post = posts.post_id
            WHERE posts.post_id = :id
        SQL);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return [
            'id' => $result['post_id'],
            'title' => $result['title'],
            'slug' => $result['slug'],
            'content' => $result['content'],
            'thumbnail' => $result['thumbnail'],
            'author' => $result['author'],
            'posted_at' => $result['posted_at'],
            'categories' => [
                'id' => $result['category_id'],
                'name' => $result['name'],
            ]
        ];
    }

    public function all(): array
    {
        //$stmt = $this->connection->query('SELECT * FROM posts');
        $stmt = $this->connection->query(<<<SQL
        SELECT posts.*, categories.*, posts_categories.* FROM posts_categories
        JOIN categories
        ON posts_categories.id_category = categories.category_id
        JOIN posts
        ON posts_categories.id_post = posts.post_id                 
        SQL);

        $stmt->execute();

        $posts = [];

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $posts[] = [
                'id' => $row['post_id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'content' => $row['content'],
                'thumbnail' => $row['thumbnail'],
                'author' => $row['author'],
                'posted_at' => $row['posted_at'],
                'categories' => [
                    'id' => $row['id_category'],
                    'name' => $row['name']
                ]
            ];
        }
        return $posts;
    }

    public function remove(string $id): string
    {
        $stmt = $this->connection->prepare('DELETE FROM posts WHERE post_id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return "$id deleted";
    }

    public function update(string $id, array $data): string
    {
        $stmt = $this->connection->prepare('UPDATE posts SET title=:title, content=:content, thumbnail=:thumbnail WHERE post_id=:id');

        $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':thumbnail' => $data['thumbnail'],
            ':id' => $id,
        ]);

        return "Post: successfully updated";
    }
}