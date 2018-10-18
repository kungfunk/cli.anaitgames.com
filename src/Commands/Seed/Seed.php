<?php
namespace Commands\Seed;

use Models\User;
use Database\Repositories\UserRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class Seed
{
    private const DEFAULT_USER_NUMBER = 100;
    private $progressBar;

    public function __construct($output)
    {
        $this->progressBar = new ProgressBar($output, self::DEFAULT_USER_NUMBER);
    }

    public function populate($database)
    {
        $insertedUsers = $this->populateUsers($database, self::DEFAULT_USER_NUMBER);

        return count($insertedUsers);
    }

    private function populateUsers($database, $iterations)
    {
        $userRepository = new UserRepository($database);

        $this->progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $user = new User();
            $userRepository->save($user);

            $this->progressBar->advance();
        }

        $this->progressBar->finish();

        return $userRepository->getInsertedIds();
    }
}
