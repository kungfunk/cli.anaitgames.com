<?php
namespace Models\Factories;

use Models\User;
use Faker\Factory;

class UserFactory
{
    public static function create($name, $email, $password, $username, $role, $patreon_level, $avatar, $rank, $twitter, $created_at = null)
    {
        return new User($name, $email, $password, $username, $role, $patreon_level, $avatar, $rank, $twitter, $created_at);
    }

    public static function fake()
    {
        $faker = Factory::create();
        $user = new User(
            $faker->name,
            $faker->email,
            password_hash('1234', PASSWORD_DEFAULT),
            $faker->userName,
            $faker->randomElement(['user' ,'moderator', 'editor', 'admin', 'superadmin']),
            $faker->numberBetween(1, 3),
            $faker->imageUrl($width = 100, $height = 100),
            $faker->sentence(3),
            $faker->userName
        );

        return $user;
    }
}
