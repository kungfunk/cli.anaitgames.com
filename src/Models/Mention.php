<?php
namespace Models;

use Faker\Factory;

class Mention
{
    public $user_from_id;
    public $user_to_id;
    public $comment_id;
    public $checked;

    public function __construct($commentIds, $userIds)
    {
        $faker = Factory::create();

        $this->user_from_id = $faker->randomElement($userIds);
        $this->user_to_id = $faker->randomElement($userIds);
        $this->comment_id = $faker->randomElement($commentIds);
        $this->checked =  $faker->numberBetween(0, 1);
    }
}
