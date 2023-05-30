<?php

namespace Nicolasps\UsersAPI\Model;

class User extends Model
{
    public string $tableName = 'user';

    public array $fields = [
        'email',
        'username',
        'password'
    ];
}