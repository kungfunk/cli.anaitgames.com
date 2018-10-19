<?php
namespace Models;

use Faker\Factory;

class IpBan
{
    public $user_id;
    public $expires;
    public $ip;
    public $reason;

    public function __construct($userIds)
    {
        $faker = Factory::create();

        $this->banned_by_id = $faker->randomElement($userIds);
        $this->expires = $faker->dateTimeThisYear()->format('Y-m-d H:i:s');
        $this->ip = $faker->ipv4;
        $this->reason = $faker->sentence(20);
    }
}
