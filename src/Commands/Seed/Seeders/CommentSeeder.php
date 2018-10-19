<?php
namespace Commands\Seed\Seeders;

use Models\Comment;
use Database\Repositories\CommentRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class CommentSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations, array $postIds, array $userIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $post = new Comment($postIds, $userIds);
            $this->repository->save($post);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
