<?php

namespace BlogRestApi;
use PDO;

class connection{
    public static function connect():PDO
    {
        $dsn = 'mysql:dbname=blog_api;host=localhost';
        $user = 'root';
        $password = '';
        return new PDO($dsn, $user, $password);
    }
}