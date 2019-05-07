<?php

namespace Commands\Seed;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSeedCommand extends Command
{
    protected $commandName = 'seed:create';
    protected $commandDescription = "Create database seed file";

    protected $commandArgumentName = "seed_name";
    protected $commandArgumentNameDescription = "Name of your seed file";

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

        $commandResult = shell_exec("vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "phinx seed:create $name -c db/config/config.php");

        $output->writeln($commandResult);
    }
}
