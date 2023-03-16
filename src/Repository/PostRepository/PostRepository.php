<?php

namespace BlogRestApi\Repository\PostRepository;

use BlogRestApi\Entity\Post;

interface PostRepository
{
    public function store(Post $post):string;
    public function get(string $id):array;
    public function all():array;
    public function delete(string $id):void;
    public function update(string $id, array $args):void;
}