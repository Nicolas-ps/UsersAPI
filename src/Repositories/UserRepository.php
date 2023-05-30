<?php

namespace Nicolasps\UsersAPI\Repositories;

use Nicolasps\UsersAPI\Model\Model;

class UserRepository extends Repository
{
    public Model $model;

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function getUserByEmail(string $email)
    {
        $pdo = $GLOBALS['PDO'];
        $stmt = $pdo->prepare('SELECT * FROM ' . $this->model->tableName . ' WHERE email = ?');
        $stmt->bindParam(1, $email);

        if ($stmt->execute()) {
            $user = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return reset($user);
        }

        return false;
    }


    public function getUserByUsername(string $username)
    {
        $pdo = $GLOBALS['PDO'];
        $stmt = $pdo->prepare('SELECT * FROM ' . $this->model->tableName . ' WHERE username = ?');
        $stmt->bindParam(1, $username);

        if ($stmt->execute()) {
            $user = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return reset($user);
        }

        return false;
    }

    public function consumeDrink(int $userId, int $drinkId): bool
    {
        $pdo = $GLOBALS['PDO'];
        $query = "INSERT INTO consummation (user_id, drink_id) values (?, ?)";
        $stmt = $pdo->prepare($query);

        if ($stmt->execute([$drinkId, $userId])) {
            return true;
        }

        return false;
    }

    public function drinkCount(int $userId): int|false
    {
        $pdo = $GLOBALS['PDO'];
        $query = "SELECT COUNT(c.id) as drinkcount FROM consummation c inner join `user` u on c.user_id = u.id ";
        $query .= "WHERE u.id = ?";

        $stmt = $pdo->prepare($query);

        if ($stmt->execute([$userId])) {
            return reset($stmt->fetchAll(\PDO::FETCH_OBJ))->drinkcount;
        }

        return false;
    }

    public function AllUsersWithDrinkCount(): array|false
    {
        $pdo = $GLOBALS['PDO'];
        $query = "SELECT u.id, u.email, u.username, count(c.id) as drinkcount FROM `user` u ";
        $query .= "left join consummation c on u.id = c.user_id group by u.id ;";
        $stmt = $pdo->prepare($query);

        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }

        return false;
    }

    public function userRegistrationPerDay(): array|false
    {
        $pdo = $GLOBALS['PDO'];
        $query = 'SELECT
                DATE_FORMAT(CONCAT(YEAR(u.created_at), "-", MONTH(u.created_at), "-", DAY(u.created_at)), "%Y-%m-%d") as `date`,
                    COUNT(u.id) AS amount_of_records
                FROM	
                    user u
                GROUP BY
                    `date`; '
        ;

        $stmt = $pdo->prepare($query);

        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }

        return false;
    }

    public function userConsummation(string $from = '', string $to = ''): array|false
    {
        $pdo = $GLOBALS['PDO'];
        $query = 'SELECT
                    u.id,
                    u.username,
                    count(c.id) as drinkcount,
                    DATE_FORMAT(CONCAT(YEAR(c.created_at), "-", MONTH(c.created_at), "-", DAY(c.created_at)), "%Y-%m-%d") as `date`
                FROM
                    consummation c
                INNER JOIN user u ON
                    u.id = c.user_id'
            ;

        $binds = [];
        if (! empty($from) && ! empty($to)) {
            $from = (new \DateTime($from))->format('Y-m-d H:i:s');
            $to = (new \DateTime($to))->add(new \DateInterval('PT23H59M59S'))->format('Y-m-d H:i:s');

            $query .= ' WHERE
	                c.created_at between ? and ?';
            ;

            $binds = [$from, $to];
        }

        $query .= ' GROUP BY
                    u.id,
                    `date`
                    ORDER BY drinkcount DESC'
        ;

        $stmt = $pdo->prepare($query);

        if ($stmt->execute($binds)) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }

        return false;
    }
}