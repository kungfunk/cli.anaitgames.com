<?php
namespace Commands\Seed\Seeders;

use Models\Factories\UserFactory;
use Database\Repositories\UserRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class UserSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $user = UserFactory::fake();
            $this->repository->save($user);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
