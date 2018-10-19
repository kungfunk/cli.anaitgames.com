<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\Tag;

class TagRepository extends PDORepository
{
    public function save(Tag $tag)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `tags` (`name`, `slug`) VALUES (?, ?)"
        );
        $result = $statement->execute([
            $tag->name,
            $tag->slug,
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }

    public function addRelationship($tagId, $postId)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `posts_tags` (`post_id`, `tag_id`) VALUES (?, ?)"
        );
        return $statement->execute([$tagId, $postId]);
    }
}
