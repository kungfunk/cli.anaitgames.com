<?php
namespace Commands\Migrate;

use Comands\Migrate\Migrate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;

use Database\PDOConnectorFactory;

class MigrateCommand extends Command
{
    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Migrate from old schema database to new one.')
            ->addArgument('old_database', InputArgument::REQUIRED, 'Old schema database')
            ->addArgument('new_database', InputArgument::REQUIRED, 'New schema database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oldDatabase = $input->getArgument('old_database');
        $newDatabase = $input->getArgument('new_database');

        $progressBar = new ProgressBar($output);
        $progressBar->setOverwrite(true);

        try {
            $connectionFrom = PDOConnectorFactory::getConnection($oldDatabase);
            $connectionTo = PDOConnectorFactory::getConnection($newDatabase);

            $migrate = new Migrate($connectionFrom, $connectionTo, $progressBar);

            $output->writeln("Cleaning up old database...");
            $migrate->cleanUp();

            $output->writeln("Migrating users...");
            $migrate->migrateUsers();

            $output->writeln("Migrating posts...");
            $migrate->migratePosts();

            $output->writeln("Migrating tags...");
            $migrate->migrateTags();

            $output->writeln("Migration completed.");
        } catch (\Exception $exception) {
            $output->writeln("Error migrating the database: {$exception->getMessage()}");
        }
    }
}
