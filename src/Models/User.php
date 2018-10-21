<?php
namespace Models;

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
    public $created_at;

    public function __construct($name, $email, $password, $username, $role, $patreon_level, $avatar, $rank, $twitter, $created_at = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->role = $role;
        $this->patreon_level = $patreon_level;
        $this->avatar = $avatar;
        $this->rank = $rank;
        $this->twitter = $twitter;
        $this->created_at = $created_at;
    }
}
