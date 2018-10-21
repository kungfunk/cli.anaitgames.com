<?php
namespace Models\Factories;

use Models\Post;
use Faker\Factory;

class PostFactory
{
    public static function create($user_id, $category_id, $status, $publish_date, $title, $subtitle, $slug, $body, $excerpt, $original_author, $score, $num_views, $metadata)
    {
        return new Post($user_id, $category_id, $status, $publish_date, $title, $subtitle, $slug, $body, $excerpt, $original_author, $score, $num_views, $metadata);
    }

    public static function fake($userIds)
    {
        $faker = Factory::create();
        $user = new Post(
            $faker->randomElement($userIds),
            $faker->numberBetween(1, 5),
            $faker->randomElement(['draft', 'published', 'trash']),
            $faker
                ->dateTimeBetween($startDate = '-1 years', $endDate = '+1 month')
                ->format('Y-m-d H:i:s'),
            $faker->sentence(5),
            $faker->sentence(8),
            $faker->slug(5),
            $faker->text(500),
            $faker->text(50),
            null,
            null,
            $faker->numberBetween(1, 1000),
            null
        );

        return $user;
    }
}
