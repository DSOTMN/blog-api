<?php

namespace BlogRestApi\Entity;
use DateTimeImmutable;

class Post
{
    public function __construct(
        private string $id,
        private string $title,
        private string $slug,
        private string $content,
        private string $thumbnail,
        private string $author,
        private mixed $postedAt = NULL
    )
    {
    }

    public function id():string
    {
        return $this->id;
    }
    public function title():string
    {
        return $this->title;
    }
    public function slug():string
    {
        return $this->slug;
    }
    public function content():string
    {
        return $this->content;
    }
    public function thumbnail():string
    {
        return $this->thumbnail;
    }
    public function author():string
    {
        return $this->author;
    }
    public function postedAt():\DateTimeImmutable
    {
        if(is_string($this->postedAt)){
            return new DateTimeImmutable($this->postedAt);
        }
        return new DateTimeImmutable("now");
    }
}