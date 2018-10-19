<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\User;

class UserRepository extends PDORepository
{
    public function save(User $user)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `users` (`name`, `email`, `password`, `username`, `role`, `patreon_level`, `avatar`, `rank`, `twitter`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
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
}
