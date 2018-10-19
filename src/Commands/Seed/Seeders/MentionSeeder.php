<?php
namespace Commands\Seed\Seeders;

use Models\Mention;
use Database\Repositories\MentionRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class MentionSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(MentionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations, array $commentsId, array $userIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $post = new Mention($commentsId, $userIds);
            $this->repository->save($post);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
