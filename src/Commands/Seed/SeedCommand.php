<?php
namespace Commands\Seed;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class SeedCommand extends Command
{
    protected function configure()
    {
        $this->setName('seed')
            ->setDescription('Populates any given database with new models.')
            ->addArgument('database', InputArgument::REQUIRED, 'What database do you wish populate)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $seed = new Seed();
        $database = $input->getArgument('database');

        try {
            $result = $seed->populate($database);
            $output->writeln("Database populated with {$result} rows");
        } catch (\Exception $exception) {
            $output->writeln("Error populating database: {$exception->getMessage()}");
        }
    }
}
