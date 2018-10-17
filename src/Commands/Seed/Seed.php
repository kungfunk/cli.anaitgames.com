<?php
namespace Commands\Seed;

use Models\User;
use Database\Repositories\UserRepository;

class Seed
{
    public function populate($database)
    {
        $userRepository = new UserRepository($database);

        $user = new User();

        return $userRepository->save($user);
    }
}
