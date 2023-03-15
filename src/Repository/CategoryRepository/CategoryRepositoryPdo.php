<?php

namespace BlogRestApi\Repository\CategoryRepository;

use BlogRestApi\Entity\Category;
use PDO;

class CategoryRepositoryPdo implements CategoryInterface
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function store(array $inputs): string
    {
        $stmt = $this->connection->prepare('INSERT INTO categories VALUES(:id, :name, :description)');

        $id = uniqid('category_');
        $category = new Category(
            $id,
            $inputs['name'],
            $inputs['description']
        );

        $stmt->execute([
           ':id' => $category->id(),
           ':name' => $category->name(),
           ':description' => $category->description()
        ]);

        return "Post successfully created";
    }

    public function get(string $id): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM categories WHERE category_id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        $stmt = $this->connection->query('SELECT * FROM categories');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(string $id): string
    {
        $stmt = $this->connection->prepare('DELETE FROM categories WHERE id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $id;
    }

    public function update(string $id, array $data):void
    {
        $stmt = $this->connection->prepare('UPDATE categories SET name=:name, description=:description WHERE id=:id');

        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':id' => $id
        ]);
    }
}