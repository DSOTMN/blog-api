<?php

namespace BlogRestApi\Repository\CategoryRepository;

interface CategoryInterface
{
    public function store(array $inputs): string;
    public function get(string $id): array;
    public function all(): array;
    public function delete(string $id): string;
    public function update(string $id, array $data):void;
}