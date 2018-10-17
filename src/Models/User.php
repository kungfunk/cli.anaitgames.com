<?php
namespace Models;

use Faker\Factory;

class User
{
    public $name;
    public $email;
    public $password;
    public $username;
    public $role;
    public $patreon_level;
    public $avatar;
    public $rank;
    public $twitter;

    public function __construct()
    {
        $faker = Factory::create();

        $this->name = $faker->name;
        $this->email = $faker->email;
        $this->password = password_hash('1234', PASSWORD_DEFAULT);
        $this->username = $faker->userName;
        $this->role = $faker->randomElement(['user' ,'moderator', 'editor', 'admin', 'superadmin']);
        $this->patreon_level = $faker->numberBetween(1, 3);
        $this->avatar = $faker->imageUrl($width = 100, $height = 100);
        $this->rank = $faker->sentence(3);
        $this->twitter = '@' . $faker->userName;
    }
}