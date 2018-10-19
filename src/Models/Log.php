<?php
namespace Models;

use Faker\Factory;

class Log
{
    public $user_id;
    public $level;
    public $message;
    public $context;
    public $extra;

    public function __construct($userIds)
    {
        $faker = Factory::create();

        $this->user_id = $faker->randomElement($userIds);
        $this->level = $faker->randomElement(['info' ,'notice', 'warning', 'error']);
        $this->message = $faker->sentence(20);
        $this->context = null;
        $this->extra = null;
    }
}
