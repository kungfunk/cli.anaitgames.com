<?php
namespace Commands\Seed\Seeders;

use Models\CommentReport;
use Database\Repositories\CommentReportRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class CommentReportSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(CommentReportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations, array $commentIds, array $userIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $report = new CommentReport($commentIds, $userIds);
            $this->repository->save($report);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
