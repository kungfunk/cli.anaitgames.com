<?php
namespace Models;

use Faker\Factory;

class Ban
{
    public $user_id;
    public $banned_by_id;
    public $expires;
    public $reason;

    public function __construct($userIds)
    {
        $faker = Factory::create();

        $this->user_id = $faker->randomElement($userIds);
        $this->banned_by_id = $faker->randomElement($userIds);
        $this->expires = $faker->dateTimeThisYear()->format('Y-m-d H:i:s');
        $this->reason = $faker->sentence(20);
    }
}
