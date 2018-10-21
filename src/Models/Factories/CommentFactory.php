<?php
namespace Models\Factories;

use Models\Comment;
use Faker\Factory;

class CommentFactory
{
    public static function create($post_id, $user_id, $body, $created_at = null)
    {
        return new Comment($post_id, $user_id, $body, $created_at);
    }

    public static function fake($postIds, $userIds)
    {
        $faker = Factory::create();
        $comment = new Comment(
            $faker->randomElement($postIds),
            $faker->randomElement($userIds),
            $faker->text(500)
        );

        return $comment;
    }
}
