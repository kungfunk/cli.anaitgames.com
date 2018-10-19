<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\Post;

class PostRepository extends PDORepository
{
    public function save(Post $post)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `posts` (`user_id`, `category_id`, `status`, `publish_date`, `title`, `subtitle`, `slug`, `body`, `excerpt`, `original_author`, `score`, `num_views`, `metadata`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $result = $statement->execute([
            $post->user_id,
            $post->category_id,
            $post->status,
            $post->publish_date,
            $post->title,
            $post->subtitle,
            $post->slug,
            $post->body,
            $post->excerpt,
            $post->original_author,
            $post->score,
            $post->num_views,
            $post->metadata
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }
}
