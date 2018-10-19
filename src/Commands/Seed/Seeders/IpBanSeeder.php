<?php
namespace Commands\Seed\Seeders;

use Models\IpBan;
use Database\Repositories\IpBanRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class IpBanSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(IpBanRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations, array $userIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $ipBan = new IpBan($userIds);
            $this->repository->save($ipBan);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
