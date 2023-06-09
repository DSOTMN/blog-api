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

    public function get(string $slug): array
    {
        $stmt = $this->connection->prepare(<<<SQL
            SELECT posts.*, categories.*
            FROM posts
            JOIN posts_categories
            ON posts.post_id = posts_categories.id_post
            JOIN categories
            ON posts_categories.id_category = categories.category_id
            WHERE posts.slug = :slug
        SQL);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $post = [];
        if(!$result){
            return $post;
        }

        $categories = [];

        foreach ($result as $row) {
            if (!isset($post['id'])) {
                $post = [
                    'id' => $row['post_id'],
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'content' => $row['content'],
                    'thumbnail' => $row['thumbnail'],
                    'author' => $row['author'],
                    'posted_at' => $row['posted_at']
                ];
            }

            $categories[] = [
                'id' => $row['category_id'],
                'name' => $row['name'],
                'description' => $row['description']
            ];
        }

        $post['categories'] = $categories;
        return $post;
    }

    public function all(): array
    {
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
                'category' => [
                    'id' => $row['id_category'],
                    'name' => $row['name']
                ]
            ];
        }
        return $posts;
    }

    public function delete(string $slug): void
    {
        $stmt = $this->connection->prepare('DELETE FROM posts WHERE slug=:slug');
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
    }

    public function update(string $slug, array $data): void
    {
        $stmt = $this->connection->prepare('UPDATE posts SET title=:title, content=:content, thumbnail=:thumbnail WHERE slug=:slug');

        $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':thumbnail' => $data['thumbnail'],
            ':slug' => $slug,
        ]);
    }
}