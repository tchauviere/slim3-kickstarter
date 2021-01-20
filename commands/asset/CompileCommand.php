<?php

namespace Commands\Asset;

require __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class CompileCommand extends Command
{
    protected $commandName = 'assets:compile';
    protected $commandDescription = "Compile all your sass and js assets";

    protected $commandArgumentType = "type";
    protected $commandArgumentTypeDescription = "(Optional) Select type of asset you want to compile 'js' or 'scss', if not specified it will compile both of them";

    protected $commandOptionWatch = "watch";
    protected $commandOptionWatchShortcut = "w";
    protected $commandOptionWatchDescription = "(Optionnal) If specified, it will watch for file change and compile whenever a change is made or a file added";

    private $filesMTimes = [];

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentType,
                InputArgument::OPTIONAL,
                $this->commandArgumentTypeDescription,
                null
            )->addOption(
                $this->commandOptionWatch,
                $this->commandOptionWatchShortcut,
                InputOption::VALUE_OPTIONAL,
                $this->commandOptionWatchDescription,
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseAssetsDir = $assetsDir = getenv('BASE_DIR').DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR;
        $baseOutputDir = getenv('BASE_DIR').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
        $scss = new \ScssPhp\ScssPhp\Compiler();
        $type = strtolower($input->getArgument($this->commandArgumentType));
        $watch = $input->hasParameterOption('--'.$this->commandOptionWatch) || $input->hasParameterOption('-'.$this->commandOptionWatchShortcut);

        if ($type) {
            if (!in_array($type, ['js', 'scss'])) {
                throw new \Exception("'type' argument can only be 'js' or 'scss'");
                exit;
            }

            $assetsDir .= $type.DIRECTORY_SEPARATOR.'*';
        } else {
            $assetsDir .= '*';
        }

        if (!$watch) {
            $filesToCompile = $this->globRecursive($assetsDir);
            $totalFiles = count($filesToCompile);
            $output->writeln("<comment>********************************</comment>");
            $output->writeln("<info>Files to compute :</info> $totalFiles");
            $output->writeln("<comment>********************************</comment>\n");
            $fileCount = 1;
            foreach ($filesToCompile as $fileToCompile) {
                $pathInfo = pathinfo($fileToCompile);

                if (strtolower($pathInfo['extension']) === 'js') {
                    $progressBar = new ProgressBar($output, 4);
                    $progressBar->setFormat('[%bar%] %percent:3s%%');
                    $output->writeln("<comment>[$fileCount/$totalFiles] ".basename($fileToCompile)."</comment>");
                    $progressBar->start();
                    $minifier = new \MatthiasMullie\Minify\JS($fileToCompile);
                    $progressBar->advance();
                    $minifiedOutputDir = str_replace($baseAssetsDir, $baseOutputDir, $pathInfo['dirname']);
                    $progressBar->advance();
                    $minifiedOutputPath = $minifiedOutputDir.DIRECTORY_SEPARATOR.$pathInfo['filename'].'.min.js';
                    $progressBar->advance();
                    @mkdir($minifiedOutputDir, 0777, true);
                    $progressBar->advance();
                    file_put_contents($minifiedOutputPath, $minifier->minify());
                    $progressBar->finish();
                    $output->writeln("\n<info>File compiled to : $minifiedOutputPath</info>\n");
                    $fileCount++;
                }

                if (strtolower($pathInfo['extension']) === 'scss') {
                    $progressBar = new ProgressBar($output, 6);
                    $progressBar->setFormat('[%bar%] %percent:3s%%');
                    $output->writeln("<comment>[$fileCount/$totalFiles] ".basename($fileToCompile)."</comment>");
                    $progressBar->start();
                    $sassContent = file_get_contents($fileToCompile);
                    $progressBar->advance();
                    $compiledContent = $scss->compile($sassContent);
                    $progressBar->advance();
                    $minifier = new \MatthiasMullie\Minify\CSS($compiledContent);
                    $progressBar->advance();
                    $minifiedOutputDir = str_replace(
                        $baseAssetsDir.'scss',
                        $baseOutputDir.'css',
                        $pathInfo['dirname']
                    );
                    $progressBar->advance();
                    $minifiedOutputPath = $minifiedOutputDir.DIRECTORY_SEPARATOR.$pathInfo['filename'].'.min.css';
                    $progressBar->advance();
                    @mkdir($minifiedOutputDir, 0777, true);
                    $progressBar->advance();
                    file_put_contents($minifiedOutputPath, $minifier->minify());
                    $progressBar->finish();
                    $output->writeln("\n<info>File compiled to : $minifiedOutputPath</info>\n");
                    $fileCount++;
                }
            }

        } else {
            // Save filemtime of all files
            $filesToCompile = $this->globRecursive($assetsDir);
            $output->writeln("<comment>***************************************************</comment>");
            $output->writeln("<info>Watching for change in the 'assets' directory ...</info>");
            $output->writeln("<comment>***************************************************</comment>\n");
            foreach ($filesToCompile as $fileToCompile) {
                $this->filesMTimes[$fileToCompile] = filemtime($fileToCompile);
            }

            $firstIteration = true;
            do {
                if (!$firstIteration) {
                    sleep(1);
                }
                foreach ($filesToCompile as $key => $fileToCompile) {

                    $newCheckFileMTime = @filemtime($fileToCompile);

                    if (!$newCheckFileMTime) {
                        unset($this->filesMTimes[$fileToCompile]);
                        unset($filesToCompile[$key]);
                        continue;
                    }

                    if ($newCheckFileMTime !== $this->filesMTimes[$fileToCompile]) {
                        // Update filemtime of the file
                        $this->filesMTimes[$fileToCompile] = filemtime($fileToCompile);

                        $pathInfo = pathinfo($fileToCompile);

                        if (strtolower($pathInfo['extension']) === 'js') {
                            $progressBar = new ProgressBar($output, 4);
                            $progressBar->setFormat('[%bar%] %percent:3s%%');
                            $output->writeln("<comment>[".date('H:i:s')."] Compile : ".basename($fileToCompile)."</comment>");
                            $progressBar->start();
                            $minifier = new \MatthiasMullie\Minify\JS($fileToCompile);
                            $progressBar->advance();
                            $minifiedOutputDir = str_replace($baseAssetsDir, $baseOutputDir, $pathInfo['dirname']);
                            $progressBar->advance();
                            $minifiedOutputPath = $minifiedOutputDir.DIRECTORY_SEPARATOR.$pathInfo['filename'].'.min.js';
                            $progressBar->advance();
                            @mkdir($minifiedOutputDir, 0777, true);
                            $progressBar->advance();
                            file_put_contents($minifiedOutputPath, $minifier->minify());
                            $progressBar->finish();
                            $output->writeln("\n<info>File compiled to : $minifiedOutputPath</info>\n");
                        }

                        if (strtolower($pathInfo['extension']) === 'scss') {
                            $progressBar = new ProgressBar($output, 6);
                            $progressBar->setFormat('[%bar%] %percent:3s%%');
                            $output->writeln("<comment>[".date('H:i:s')."] Compile : ".basename($fileToCompile)."</comment>");
                            $progressBar->start();
                            $sassContent = file_get_contents($fileToCompile);
                            $progressBar->advance();
                            $compiledContent = $scss->compile($sassContent);
                            $progressBar->advance();
                            $minifier = new \MatthiasMullie\Minify\CSS($compiledContent);
                            $progressBar->advance();
                            $minifiedOutputDir = str_replace(
                                $baseAssetsDir.'scss',
                                $baseOutputDir.'css',
                                $pathInfo['dirname']
                            );
                            $progressBar->advance();
                            $minifiedOutputPath = $minifiedOutputDir.DIRECTORY_SEPARATOR.$pathInfo['filename'].'.min.css';
                            $progressBar->advance();
                            @mkdir($minifiedOutputDir, 0777, true);
                            $progressBar->advance();
                            file_put_contents($minifiedOutputPath, $minifier->minify());
                            $progressBar->finish();
                            $output->writeln("\n<info>File compiled to : $minifiedOutputPath</info>\n");
                        }

                    }
                }
                if ($firstIteration) {
                    sleep(1);
                }
            } while(1);

        }

        /*
        $commandResult = shell_exec("php ./assets/compile --ansi");

        $output->writeln($commandResult);*/
    }

    // Does not support flag GLOB_BRACE
    private function globRecursive($pattern)
    {
        $files = array_filter(glob($pattern), 'is_file');

        foreach (glob(dirname($pattern).DIRECTORY_SEPARATOR.'*') as $dir) {
            $files = array_merge($files, $this->globRecursive($dir.DIRECTORY_SEPARATOR.basename($pattern)));
        }
        return $files;
    }
}
