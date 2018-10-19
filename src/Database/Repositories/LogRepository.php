<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\Log;

class LogRepository extends PDORepository
{
    public function save(Log $log)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `logs` (`user_id`, `level`, `message`, `context`, `extra`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $result = $statement->execute([
            $log->user_id,
            $log->level,
            $log->message,
            $log->context,
            $log->extra
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }
}
