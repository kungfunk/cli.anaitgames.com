<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\CommentReport;

class CommentReportRepository extends PDORepository
{
    public function save(CommentReport $commentReport)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `comment_reports` (`comment_id`, `user_id`, `body`, `checked`, `created_at`) VALUES (?, ?, ?, ?, NOW())"
        );
        $result = $statement->execute([
            $commentReport->comment_id,
            $commentReport->user_id,
            $commentReport->body,
            $commentReport->checked
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }
}
