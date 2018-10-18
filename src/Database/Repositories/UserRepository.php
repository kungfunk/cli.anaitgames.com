<?php
namespace Database\Repositories;

use Database\PDOConnector;
use Models\User;

class UserRepository
{
    private $connection;
    private $insertedIds = [];

    public function __construct($database)
    {
        $PDOConnector = new PDOConnector($database);
        $this->connection = $PDOConnector->getConnection();
    }

    public function save(User $user)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `users` (`name`, `email`, `password`, `username`, `role`, `patreon_level`, `avatar`, `rank`, `twitter`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $result = $statement->execute([
            $user->name,
            $user->email,
            $user->password,
            $user->username,
            $user->role,
            $user->patreon_level,
            $user->avatar,
            $user->rank,
            $user->twitter,
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }

    public function getInsertedIds()
    {
        return $this->insertedIds;
    }
}
