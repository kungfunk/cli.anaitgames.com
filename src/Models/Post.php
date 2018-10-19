<?php
namespace Models;

use Faker\Factory;

class Post
{
    public $user_id;
    public $category_id;
    public $status;
    public $publish_date;
    public $title;
    public $subtitle;
    public $slug;
    public $body;
    public $excerpt;
    public $original_author;
    public $score;
    public $num_views;
    public $metadata;

    public function __construct($userIds)
    {
        $faker = Factory::create();

        $this->user_id = $faker->randomElement($userIds);
        $this->category_id = $faker->numberBetween(1, 5);
        $this->status = $faker->randomElement(['draft', 'published', 'trash']);
        $this->publish_date = $faker
            ->dateTimeBetween($startDate = '-1 years', $endDate = '+1 month')
            ->format('Y-m-d H:i:s');
        $this->title = $faker->sentence(5);
        $this->subtitle = $faker->sentence(8);
        $this->slug = $faker->slug(5);
        $this->body = $faker->text(500);
        $this->excerpt = $faker->text(50);
        $this->original_author = null;
        $this->score = null;
        $this->num_views = $faker->numberBetween(1, 1000);
        $this->metadata = null;
    }
}
