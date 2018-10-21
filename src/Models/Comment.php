<?php
namespace Models;

class Comment
{
    public $post_id;
    public $user_id;
    public $body;
    public $created_at;

    public function __construct($post_id, $user_id, $body, $created_at = null)
    {
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->body = $body;
        $this->created_at = $created_at;
    }
}
