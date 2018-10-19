<?php
namespace Models;

use Faker\Factory;

class Comment
{
    public $post_id;
    public $user_id;
    public $body;

    public function __construct($postIds, $userIds)
    {
        $faker = Factory::create();

        $this->post_id = $faker->randomElement($postIds);
        $this->user_id = $faker->randomElement($userIds);
        $this->body = $faker->text(500);
    }
}
