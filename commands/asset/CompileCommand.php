<?php

namespace Commands\Asset;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompileCommand extends Command
{
    protected $commandName = 'assets:compile';
    protected $commandDescription = "Compile all your sass and js assets";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandResult = shell_exec("php ./assets/compile");

        $output->writeln($commandResult);
    }
}
