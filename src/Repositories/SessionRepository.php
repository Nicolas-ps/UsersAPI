<?php

namespace Nicolasps\UsersAPI\Repositories;

use Nicolasps\UsersAPI\Model\Model;

class SessionRepository extends Repository
{
    public Model $model;
    
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function getBySessionId(string $sessionId): object|false
    {
        $pdo = getGlobal('PDO');

        $query = "SELECT * FROM " . $this->model->tableName . " WHERE session_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $sessionId);

        if ($stmt->execute()) {
            $object = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return reset($object);
        }

        return false;
    }
}