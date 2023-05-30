<?php

namespace Nicolasps\UsersAPI\Model;

class Drink extends Model
{
    public string $tableName = 'drink';

    public array $fields = [
        'name',
    ];
}