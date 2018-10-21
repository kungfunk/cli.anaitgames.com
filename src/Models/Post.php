<?php
namespace Models;

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

    public function __construct($user_id, $category_id, $status, $publish_date, $title, $subtitle, $slug, $body, $excerpt, $original_author, $score, $num_views, $metadata)
    {
        $this->user_id = $user_id;
        $this->category_id = $category_id;
        $this->status = $status;
        $this->publish_date = $publish_date;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->slug = $slug;
        $this->body = $body;
        $this->excerpt = $excerpt;
        $this->original_author = $original_author;
        $this->score = $score;
        $this->num_views = $num_views;
        $this->metadata = $metadata;
    }
}
