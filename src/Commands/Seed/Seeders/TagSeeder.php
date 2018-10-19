<?php
namespace Commands\Seed\Seeders;

use Models\Tag;
use Database\Repositories\TagRepository;

use Symfony\Component\Console\Helper\ProgressBar;

class TagSeeder
{
    private $repository;
    public $insertedIds;

    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function populate(ProgressBar $progressBar, int $iterations): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $tag = new Tag();
            $this->repository->save($tag);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }

    public function populatePostsTagsRelationship(ProgressBar $progressBar, int $iterations, array $insertedTagIds, array $insertedPostIds): int
    {
        $progressBar->setMaxSteps($iterations);
        $progressBar->start();

        for ($i = 1; $i <= $iterations; $i++) {
            $postId = $insertedPostIds[array_rand($insertedTagIds, 1)];
            $tagId = $insertedTagIds[array_rand($insertedTagIds, 1)];
            $this->repository->addRelationship($postId, $tagId);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->insertedIds = $this->repository->getInsertedIds();

        return count($this->insertedIds);
    }
}
