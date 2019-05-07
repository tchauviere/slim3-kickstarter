<?php

namespace Commands\Secret;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSecretCommand extends Command
{
    protected $commandName = 'secret:generate';
    protected $commandDescription = "Generate a new secret into your settings.php files. Do not use it if you have already registered user with current secret, or they will not be able to login anymore !";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $settingsContent = file_get_contents(__DIR__.'/../../src/config/settings.php');
        $newSecret = "'".sha1(time().random_bytes(12))."'";
        $re = '/(?<=\'secret\' => )(.*)(?=,)/m';
        $newSettingsContent = preg_replace($re, $newSecret, $settingsContent);

        if (is_null($newSettingsContent) || $newSettingsContent == $settingsContent) {
            $output->writeln("We were unable to replace old secret with the new one automatically !");
            exit;
        }

        if (!file_put_contents(__DIR__.'/../../src/config/settings.php', $newSettingsContent)) {
            $output->writeln("We were unable write into your 'settings.php' file !");
            exit;
        }

        $output->writeln("New secret generated succesfully ! $newSecret");
    }
}
