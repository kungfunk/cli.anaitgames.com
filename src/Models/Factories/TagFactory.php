<?php
namespace Models\Factories;

use Models\Tag;
use Faker\Factory;

class TagFactory
{
    public static function create($name, $slug)
    {
        return new Tag($name, $slug);
    }

    public static function fake()
    {
        $faker = Factory::create();

        $tag = new Tag(
            $faker->sentence(3),
            $faker->slug(3)
        );

        return $tag;
    }
}
