<?php
namespace Commands\Seed\Seeders;

use Models\Log;
use Database\Repositories\LogRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class LogSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(LogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations, array $userIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $log = new Log($userIds);
            $this->repository->save($log);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
