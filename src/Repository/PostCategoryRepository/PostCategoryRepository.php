<?php

namespace BlogRestApi\Repository\PostCategoryRepository;

use BlogRestApi\Entity\Post;

interface PostCategoryRepository
{
    public function store(Post $post);
}