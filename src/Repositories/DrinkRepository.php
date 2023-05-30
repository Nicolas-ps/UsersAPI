<?php

namespace Nicolasps\UsersAPI\Repositories;

use Nicolasps\UsersAPI\Model\Model;

class DrinkRepository extends Repository
{
    public Model $model;

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }
}