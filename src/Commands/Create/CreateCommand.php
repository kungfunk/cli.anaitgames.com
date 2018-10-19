<?php
namespace Commands\Create;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateCommand extends Command
{
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Clear database with new schema.')
            ->addArgument('database', InputArgument::REQUIRED, 'What database do you wish create)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $input->getArgument('database');
        $create = new Create($database);

        try {
            $create->create();
            $output->writeln("Database {$database} created.");
        } catch (\Exception $exception) {
            $output->writeln("Error creating the database: {$exception->getMessage()}");
        }
    }
}
