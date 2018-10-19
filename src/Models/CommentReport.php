<?php
namespace Models;

use Faker\Factory;

class CommentReport
{
    public $comment_id;
    public $user_id;
    public $body;
    public $checked;

    public function __construct($commentIds, $userIds)
    {
        $faker = Factory::create();

        $this->comment_id= $faker->randomElement($commentIds);
        $this->user_id = $faker->randomElement($userIds);
        $this->body = $faker->text(255);
        $this->checked = $faker->numberBetween(0, 1);
    }
}
