<?php
namespace Commands\Seed;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;

use Database\PDOConnectorFactory;
use Database\Repositories\UserRepository;
use Database\Repositories\PostRepository;
use Database\Repositories\TagRepository;
use Database\Repositories\CommentRepository;
use Database\Repositories\CommentReportRepository;
use Database\Repositories\MentionRepository;

use Commands\Seed\Seeders\UserSeeder;
use Commands\Seed\Seeders\PostSeeder;
use Commands\Seed\Seeders\TagSeeder;
use Commands\Seed\Seeders\CommentSeeder;
use Commands\Seed\Seeders\CommentReportSeeder;
use Commands\Seed\Seeders\MentionSeeder;

class SeedCommand extends Command
{
    private const DEFAULT_USERS_NUMBER = 10;
    private const DEFAULT_POSTS_NUMBER = 10;
    private const DEFAULT_TAGS_NUMBER = 5;
    private const DEFAULT_POSTS_TAGS_NUMBER = 20;
    private const DEFAULT_COMMENTS_NUMBER = 50;
    private const DEFAULT_COMMENT_REPORTS_NUMBER = 20;
    private const DEFAULT_MENTIONS_NUMBER = 20;

    protected function configure()
    {
        $this->setName('seed')
            ->setDescription('Populates any given database with new models.')
            ->addArgument('database', InputArgument::REQUIRED, 'What database do you wish populate)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $databaseName = $input->getArgument('database');

        try {
            $connection = PDOConnectorFactory::getConnection($databaseName);
            $progressBar = new ProgressBar($output);
            $progressBar->setOverwrite(true);

            $userSeeder = new UserSeeder(new UserRepository($connection));
            $usersCount = $userSeeder->populate($progressBar, self::DEFAULT_USERS_NUMBER);
            $output->writeln("\nStep 1/8 - {$usersCount} users added");

            $postSeeder = new PostSeeder(new PostRepository($connection));
            $postsCount = $postSeeder->populate($progressBar, self::DEFAULT_POSTS_NUMBER, $userSeeder->insertedIds);
            $output->writeln("\nStep 1/8 - {$postsCount} posts added");

            $tagsSeeder = new TagSeeder(new TagRepository($connection));
            $tagsCount = $tagsSeeder->populate($progressBar, self::DEFAULT_TAGS_NUMBER);
            $output->writeln("\nStep 3/8 - {$tagsCount} tags added");

            $postsTagsCount = $tagsSeeder->populatePostsTagsRelationship($progressBar, self::DEFAULT_POSTS_TAGS_NUMBER, $postSeeder->insertedIds);
            $output->writeln("\nStep 4/8 - {$postsTagsCount} relationships added");

            $commentsSeeder = new CommentSeeder(new CommentRepository($connection));
            $commentsCount = $commentsSeeder->populate($progressBar, self::DEFAULT_COMMENTS_NUMBER, $postSeeder->insertedIds, $userSeeder->insertedIds);
            $output->writeln("\nStep 5/8 - {$commentsCount} comments added");

            $commentReportsSeeder = new CommentReportSeeder(new CommentReportRepository($connection));
            $commentsCount = $commentReportsSeeder->populate($progressBar, self::DEFAULT_COMMENT_REPORTS_NUMBER, $commentsSeeder->insertedIds, $userSeeder->insertedIds);
            $output->writeln("\nStep 6/8 - {$commentsCount} comment reports added");

            $mentionSeeder = new MentionSeeder(new MentionRepository($connection));
            $tagsCount = $mentionSeeder->populate($progressBar, self::DEFAULT_MENTIONS_NUMBER, $commentsSeeder->insertedIds, $userSeeder->insertedIds);
            $output->writeln("\nStep 7/8 - {$tagsCount} mentions added");

            $total = $usersCount + $postsCount + $tagsCount + $postsTagsCount;
            $output->writeln("Seed finished. Database populated with {$total} rows");
        } catch (\Exception $exception) {
            $output->writeln("Error populating database: {$exception->getMessage()}");
        }
    }
}
