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
        private mixed $postedAt = NULL,
        /** @param Category[] $categories */
        private readonly mixed $categories
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

    /**
     * @throws \Exception
     */
    public function postedAt():\DateTimeImmutable
    {
        if(is_string($this->postedAt)){
            return new DateTimeImmutable($this->postedAt);
        }
        return new DateTimeImmutable("now");
    }

    /** @return Category[] */
    public function categories(): mixed
    {
        return $this->categories;
    }
}