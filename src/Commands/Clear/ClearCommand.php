<?php
namespace Commands\Clear;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ClearCommand extends Command
{
    protected function configure()
    {
        $this->setName('clear')
            ->setDescription('Clear database.')
            ->addArgument('database', InputArgument::REQUIRED, 'What database do you wish clear)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $create = new Clear();
        $database = $input->getArgument('database');

        try {
            $create->create($database);
            $output->writeln("Database {$database} cleared.");
        } catch (\Exception $exception) {
            $output->writeln("Error clearing the database: {$exception->getMessage()}");
        }
    }
}
