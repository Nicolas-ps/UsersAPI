<?php

namespace Nicolasps\UsersAPI\Model;

class Session extends Model
{
    public string $tableName = 'session';

    public array $fields = [
        'user_id',
        'session_id',
        'expires_in',
    ];
}