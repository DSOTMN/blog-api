<?php

namespace BlogRestApi\Controller;

use DI\Container;
use PDO;

class FileUploadController
{
    private PDO $pdo;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('db');
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }
}