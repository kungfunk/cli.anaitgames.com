<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\Comment;

class CommentRepository extends PDORepository
{
    public function save(Comment $comment)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `comments` (`post_id`, `user_id`, `body`, `created_at`) VALUES (?, ?, ?, NOW())"
        );
        $result = $statement->execute([
            $comment->post_id,
            $comment->user_id,
            $comment->body
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }
}
