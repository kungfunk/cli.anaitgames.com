<?php
namespace Commands\Seed\Seeders;

use Models\Ban;
use Database\Repositories\BanRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class BanSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(BanRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations, array $userIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $ban = new Ban($userIds);
            $this->repository->save($ban);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
