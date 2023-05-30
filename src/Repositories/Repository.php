<?php

namespace Nicolasps\UsersAPI\Repositories;

use Nicolasps\UsersAPI\Model\Model;

abstract class Repository
{
    public Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        $pdo = $GLOBALS['PDO'];
        $stmt = $pdo->query('select * from ' . $this->model->tableName . ';');

        if ($stmt->execute()) return $stmt->fetchAll(\PDO::FETCH_OBJ);

        return false;
    }

    public function new(array $values): object|bool
    {
        $pdo = $GLOBALS['PDO'];
        $binds = implode(',', array_fill(0, count($this->model->fields), '?'));

        $query = "INSERT INTO " . $this->model->tableName . ' (' . implode(',', $this->model->fields) . ')';
        $query .= " VALUES (" . $binds . ")";

        $stmt = $pdo->prepare($query);
        if ($stmt->execute($values)) return $this->getById($pdo->lastInsertId());

        return false;
    }

    public function update(array $values, int $id): object|false
    {
        $pdo = $GLOBALS['PDO'];
        $binds = implode(',', array_fill(0, count($this->model->fields), '?'));

        $query = "UPDATE " . $this->model->tableName . ' set ';

        $fieldsAndValues = array_combine($this->model->fields, $values);
        $fields = array_keys($fieldsAndValues);
        $lastField = end($fields);

        foreach ($fieldsAndValues as $field => $value) {
            $query .= $field . " = " . "'$value'";

            if ($field !== $lastField) {
                $query .= ', ';
            }
        }

        if (! empty($id)) {
            $query .= " where id = ?;";
        }

        $stmt = $pdo->prepare($query);

        if (! empty($id)) {
            $stmt->bindParam(1, $id);
        }

        if ($stmt->execute()) return $this->getById($id);

        return false;
    }

    public function getById(int $id): object|bool
    {
        $pdo = getGlobal('PDO');

        $query = "SELECT * FROM " . $this->model->tableName . " WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            $object = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return reset($object);
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $pdo = getGlobal('PDO');

        $query = "DELETE FROM " . $this->model->tableName . " WHERE id = ?";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $id);

        return $stmt->execute();
    }
}