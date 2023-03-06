<?php

namespace BlogRestApi\Repository\PostRepository;

interface PostRepository
{
    public function store(array $inputs):string;
    public function get(string $id):array;
    public function all():array;
    public function remove(string $id):string;
    public function update(string $id, array $args):string;
}