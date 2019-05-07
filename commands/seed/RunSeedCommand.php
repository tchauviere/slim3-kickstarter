<?php

namespace Commands\Seed;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunSeedCommand extends Command
{
    protected $commandName = 'seed:run';
    protected $commandDescription = "Run your seeds files (without --seed, it will run all of them at once)";

    protected $commandOptionSeedName = "seed";
    protected $commandOptionSeedNameShortcut = "s";
    protected $commandOptionSeedNameDescription = "Specify one Seed name that will be the only one to be executed";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->addOption(
                $this->commandOptionSeedName,
                $this->commandOptionSeedNameShortcut,
                InputOption::VALUE_REQUIRED,
                $this->commandOptionSeedNameDescription,
                null
            )
            ->setDescription($this->commandDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $seedName = $input->getOption($this->commandOptionSeedName);

        $runSeedCmd = "vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "phinx seed:run -c db/config/config.php -e db";

        if ($seedName) {
            $runSeedCmd .= " -s $seedName";
        }

        $commandResult = shell_exec($runSeedCmd);

        $output->writeln($commandResult);
    }
}
