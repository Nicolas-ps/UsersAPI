<?php

namespace Nicolasps\UsersAPI\Database;

class Connection
{
    public static function init()
    {
        $databaseName = DATABASE_CONNECTION['DATABASE_NAME'];
        $host = DATABASE_CONNECTION['HOST'];
        return new \PDO(
            'mysql:dbname=' . $databaseName . ';host=' . $host,
            DATABASE_CONNECTION['USERNAME'],
            DATABASE_CONNECTION['PASSWORD']
        );
    }
}