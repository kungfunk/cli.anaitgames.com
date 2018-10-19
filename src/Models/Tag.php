<?php
namespace Models;

use Faker\Factory;

class Tag
{
    public $name;
    public $slug;

    public function __construct()
    {
        $faker = Factory::create();

        $this->name = $faker->sentence(3);
        $this->slug = $faker->slug(3);
    }
}
