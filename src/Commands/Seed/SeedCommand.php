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
use Database\Repositories\BanRepository;
use Database\Repositories\IpBanRepository;
use Database\Repositories\LogRepository;

use Commands\Seed\Seeders\UserSeeder;
use Commands\Seed\Seeders\PostSeeder;
use Commands\Seed\Seeders\TagSeeder;
use Commands\Seed\Seeders\CommentSeeder;
use Commands\Seed\Seeders\CommentReportSeeder;
use Commands\Seed\Seeders\MentionSeeder;
use Commands\Seed\Seeders\BanSeeder;
use Commands\Seed\Seeders\IpBanSeeder;
use Commands\Seed\Seeders\LogSeeder;

class SeedCommand extends Command
{
    private const DEFAULT_USERS_NUMBER = 100;
    private const DEFAULT_POSTS_NUMBER = 1000;
    private const DEFAULT_TAGS_NUMBER = 500;
    private const DEFAULT_POSTS_TAGS_NUMBER = 2000;
    private const DEFAULT_COMMENTS_NUMBER = 15000;
    private const DEFAULT_COMMENT_REPORTS_NUMBER = 200;
    private const DEFAULT_MENTIONS_NUMBER = 5000;
    private const DEFAULT_BANS_NUMBER = 100;
    private const DEFAULT_IP_BANS_NUMBER = 30;
    private const DEFAULT_LOGS_NUMBER = 5000;

    private $connection;
    private $progressBar;
    private $totalCount = 0;

    private $userIds;
    private $postIds;
    private $commentIds;
    private $tagIds;

    protected function configure()
    {
        $this->setName('seed')
            ->setDescription('Populates any given database with new models.')
            ->addArgument('database', InputArgument::REQUIRED, 'What database do you wish populate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $databaseName = $input->getArgument('database');
        $this->progressBar = new ProgressBar($output);
        $this->progressBar->setOverwrite(true);

        try {
            $this->connection = PDOConnectorFactory::getConnection($databaseName);

            $usersCount = $this->populateUsers(self::DEFAULT_USERS_NUMBER);
            $output->writeln("\nStep 1/10 - {$usersCount} users added");

            $postsCount = $this->populatePosts(self::DEFAULT_POSTS_NUMBER);
            $output->writeln("\nStep 1/10 - {$postsCount} posts added");

            $tagsCount = $this->populateTags(self::DEFAULT_TAGS_NUMBER);
            $output->writeln("\nStep 3/10 - {$tagsCount} tags added");

            $postsTagsCount = $this->populatePostsTagsRelationship(self::DEFAULT_POSTS_TAGS_NUMBER);
            $output->writeln("\nStep 4/10 - {$postsTagsCount} relationships added");

            $commentsCount = $this->populateComments(self::DEFAULT_COMMENTS_NUMBER);
            $output->writeln("\nStep 5/10 - {$commentsCount} comments added");

            $commentReportsCount = $this->populateCommentReports(self::DEFAULT_COMMENT_REPORTS_NUMBER);
            $output->writeln("\nStep 6/10 - {$commentReportsCount} comment reports added");

            $mentionsCount = $this->populateMentions(self::DEFAULT_MENTIONS_NUMBER);
            $output->writeln("\nStep 7/10 - {$mentionsCount} mentions added");

            $banCount = $this->populateBans(self::DEFAULT_BANS_NUMBER);
            $output->writeln("\nStep 8/10 - {$banCount} bans added");

            $ipBanCount = $this->populateIpBans(self::DEFAULT_IP_BANS_NUMBER);
            $output->writeln("\nStep 9/10 - {$ipBanCount} ip bans added");

            $logsCount = $this->populateLogs(self::DEFAULT_LOGS_NUMBER);
            $output->writeln("\nStep 10/10 - {$logsCount} logs added");

            $output->writeln("Seed finished. Database populated with {$this->totalCount} rows");
        } catch (\Exception $exception) {
            $output->writeln("Error populating database: {$exception->getMessage()}");
        }
    }

    private function populateUsers($iterations): int
    {
        $seeder = new UserSeeder(new UserRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations);
        $this->userIds = $seeder->insertedIds;
        $this->totalCount += $count;

        return $count;
    }

    private function populatePosts($iterations): int
    {
        $seeder = new PostSeeder(new PostRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations, $this->userIds);
        $this->postIds = $seeder->insertedIds;
        $this->totalCount += $count;

        return $count;
    }

    private function populateTags($iterations): int
    {
        $seeder = new TagSeeder(new TagRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations);
        $this->tagIds = $seeder->insertedIds;
        $this->totalCount += $count;

        return $count;
    }

    private function populatePostsTagsRelationship($iterations): int
    {
        $seeder = new TagSeeder(new TagRepository($this->connection));
        $count = $seeder->populatePostsTagsRelationship($this->progressBar, $iterations, $this->tagIds, $this->postIds);
        $this->totalCount += $count;

        return $count;
    }

    private function populateComments($iterations): int
    {
        $seeder = new CommentSeeder(new CommentRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations, $this->postIds, $this->userIds);
        $this->commentIds = $seeder->insertedIds;
        $this->totalCount += $count;

        return $count;
    }

    private function populateCommentReports($iterations): int
    {
        $seeder = new CommentReportSeeder(new CommentReportRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations, $this->commentIds, $this->userIds);
        $this->totalCount += $count;

        return $count;
    }

    private function populateMentions($iterations): int
    {
        $seeder = new MentionSeeder(new MentionRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations, $this->commentIds, $this->userIds);
        $this->totalCount += $count;

        return $count;
    }

    private function populateBans($iterations): int
    {
        $seeder = new BanSeeder(new BanRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations, $this->userIds);
        $this->totalCount += $count;

        return $count;
    }

    private function populateIpBans($iterations): int
    {
        $seeder = new IpBanSeeder(new IpBanRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations, $this->userIds);
        $this->totalCount += $count;

        return $count;
    }

    private function populateLogs($iterations): int
    {
        $seeder = new LogSeeder(new LogRepository($this->connection));
        $count = $seeder->populate($this->progressBar, $iterations, $this->userIds);
        $this->totalCount += $count;

        return $count;
    }
}
