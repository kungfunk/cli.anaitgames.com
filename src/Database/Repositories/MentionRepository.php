<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\Mention;

class MentionRepository extends PDORepository
{
    public function save(Mention $mention)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `mentions` (`user_from_id`, `user_to_id`, `comment_id`, `checked`, `created_at`) VALUES (?, ?, ?, ?, NOW())"
        );
        $result = $statement->execute([
            $mention->user_from_id,
            $mention->user_to_id,
            $mention->comment_id,
            $mention->checked
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }
}
