<?php

namespace Commands\Migration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunMigrationCommand extends Command
{
    protected $commandName = 'migration:run';
    protected $commandDescription = "Run up or down migration files";

    protected $commandArgumentDirection = "direction";
    protected $commandArgumentDirectionDescription = "Select migration 'up' or 'down' depending of what you want";

    protected $commandArgumentTarget = "target";
    protected $commandArgumentTargetDescription = "Select target timestamp, migration will stop to this timestamp, if none all migrations will be played";

    protected $commandOptionDryRun = "dry-run";
    protected $commandOptionDryRunDescription = "If specified, migration will be tested but not persisted into DB";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentDirection,
                InputArgument::REQUIRED,
                $this->commandArgumentDirectionDescription
            )
            ->addArgument(
                $this->commandArgumentTarget,
                InputArgument::OPTIONAL,
                $this->commandArgumentTargetDescription
            )->addOption(
                $this->commandOptionDryRun,
                null,
                InputOption::VALUE_OPTIONAL,
                $this->commandOptionDryRunDescription,
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $direction = strtolower($input->getArgument($this->commandArgumentDirection));
        $target = $input->getArgument($this->commandArgumentTarget);
        $dryRun = $input->getOption($this->commandOptionDryRun);


        if (!in_array($direction, ['up', 'down'])) {
            throw new \Exception('Invalid "direction" argument ! It can only accepts "up" or "down"');
        }

        if ($direction == 'up') {
            $migrateUpCmd = "vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "phinx migrate -c db/config/config.php -e db";

            if ($target) {
                $migrateUpCmd .= " -t ".$target;
            }

            if ($dryRun) {
                $migrateUpCmd .= " --dry-run";
            }

            $commandResult = shell_exec($migrateUpCmd);
        } else {
            $migrateDownCmd = "vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "phinx rollback -c db/config/config.php -e db";

            if ($target) {
                $migrateDownCmd .= " -t ".$target;
            } else if ((int)$target == 0 && !is_null($target)) {
                $migrateDownCmd .= " -t 0"; // Rollback to start
            }

            if ($dryRun) {
                $migrateDownCmd .= " --dry-run";
            }

            $commandResult = shell_exec($migrateDownCmd);
        }

        $output->writeln($commandResult);
    }
}
