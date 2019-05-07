<?php

namespace Commands\Migration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMigrationCommand extends Command
{
    protected $commandName = 'migration:create';
    protected $commandDescription = "Create database migration file";

    protected $commandArgumentName = "migration_name";
    protected $commandArgumentNameDescription = "Name of your migration file";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::REQUIRED,
                $this->commandArgumentNameDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument($this->commandArgumentName);

        $commandResult = shell_exec("vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "phinx create $name -c db/config/config.php");

        $output->writeln($commandResult);
    }
}
