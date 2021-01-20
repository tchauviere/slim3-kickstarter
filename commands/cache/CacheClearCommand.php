<?php

namespace Commands\Cache;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CacheClearCommand extends Command
{
    protected $commandName = 'cache:clear';
    protected $commandDescription = "Clear all caches";

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        $formattedLine = $formatter->formatSection(
            'Cache',
            "\n".'<comment>Cache clear</comment>'."\n"
        );
        $output->writeln("\n".$formattedLine."\n");


        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(getenv('TWIG_CACHE_PATH'), \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {

            if ($fileinfo->getFilename() === '.gitkeep') {
                continue;
            }

            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        $output->writeln("\n<info>Cache cleared !</info>\n");

    }
}

