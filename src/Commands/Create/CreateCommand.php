<?php
namespace Commands\Create;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CreateCommand extends Command
{
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Create database with new schema.')
            ->addArgument('database', InputArgument::REQUIRED, 'What database do you wish create)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $create = new Create();
        $database = $input->getArgument('database');

        try {
            $result = $create->create($database);
            $output->writeln("Database created with '{$database}' name. {$result} rows affected.");
        } catch (\Exception $exception) {
            $output->writeln("Error creating the database: {$exception->getMessage()}");
        }
    }
}
