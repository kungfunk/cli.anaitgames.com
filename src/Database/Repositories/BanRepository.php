<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\Ban;

class BanRepository extends PDORepository
{
    public function save(Ban $ban)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `bans` (`user_id`, `banned_by_id`, `expires`, `reason`, `created_at`) VALUES (?, ?, ?, ?, NOW())"
        );
        $result = $statement->execute([
            $ban->user_id,
            $ban->banned_by_id,
            $ban->expires,
            $ban->reason
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }
}
