<?php

namespace BlogRestApi\Entity;

class Category
{
    public function __construct(
        private string $id,
        private string $name,
        private string $description
    )
    {
    }
    public function id():string
    {
        return $this->id;
    }
    public function name():string
    {
        return $this->name;
    }
    public function description():string
    {
        return $this->description;
    }
}