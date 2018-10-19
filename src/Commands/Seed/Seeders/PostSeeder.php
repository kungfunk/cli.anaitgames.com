<?php
namespace Commands\Seed\Seeders;

use Models\Post;
use Database\Repositories\PostRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class PostSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations, array $userIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $post = new Post($userIds);
            $this->repository->save($post);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
