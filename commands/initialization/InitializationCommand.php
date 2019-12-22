<?php

namespace Commands\Initialization;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Composer\Script\Event;

class InitializationCommand extends Command
{
    protected $commandName = 'project:init';
    protected $commandDescription = "Initialize easily your new project";

    protected $envFileValues;
    protected $basePath;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->basePath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
        $this->envFileValues = [
            'SLIM3_MODE' => [
                'default' => 'dev',
                'question' => 'Which mode would you like to run ?',
                'user_input' => ''
            ],
            'SLIM3_ROUTE_NAME_IN_MIDDLEWARE' => [
                'default' => 'true',
                'question' => 'Access to route from middlewares ?',
                'user_input' => ''
            ],
            'SLIM3_ADD_CONTENT_LENGTH_HEADER' => [
                'default' => 'true',
                'question' => 'Add content length to response header ?',
                'user_input' => ''
            ],
            'APP_SECRET' => [
                'default' => sha1(time().random_bytes(12)),
                'question' => 'Your app secret :',
                'user_input' => ''
            ],
            'APP_LANG_PATH' => [
                'default' => $this->basePath.DIRECTORY_SEPARATOR.'lang',
                'question' => 'Your language directory path :',
                'user_input' => ''
            ],
            'APP_UPLOADED_FILE_DIRECTORY' => [
                'default' => $this->basePath.DIRECTORY_SEPARATOR.'uploads',
                'question' => 'Your uploaded files directory path :',
                'user_input' => ''
            ],
            'ADMIN_BASE_URI' => [
                'default' => 'admin',
                'question' => 'Which URI to use as base admin access ("/admin")',
                'user_input' => ''
            ],
            'TWIG_TPL_PATH' => [
                'default' => $this->basePath.DIRECTORY_SEPARATOR.'templates',
                'question' => 'Your twig templates path :',
                'user_input' => ''
            ],
            'TWIG_CACHE_PATH' => [
                'default' => $this->basePath.DIRECTORY_SEPARATOR.'cache',
                'question' => 'Your twig cache directory path :',
                'user_input' => ''
            ],
            'MONOLOG_NAME' => [
                'default' => 'app',
                'question' => 'Monolog app name :',
                'user_input' => ''
            ],
            'MONOLOG_PATH' => [
                'default' => $this->basePath.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'app.log',
                'question' => 'Monolog path to log file :',
                'user_input' => ''
            ],
            'MONOLOG_LEVEL' => [
                'default' => '\Monolog\Logger::DEBUG',
                'question' => 'Monolog log level',
                'user_input' => ''
            ],
            'DB_DRIVER' => [
                'default' => 'mysql',
                'question' => 'Your DB driver :',
                'user_input' => ''
            ],
            'DB_HOST' => [
                'default' => 'localhost',
                'question' => 'Your DB host :',
                'user_input' => ''
            ],
            'DB_PORT' => [
                'default' => '3306',
                'question' => 'Your DB port :',
                'user_input' => ''
            ],
            'DB_DATABASE' => [
                'default' => 'app_db',
                'question' => 'Your DB database name :',
                'user_input' => ''
            ],
            'DB_USERNAME' => [
                'default' => 'root',
                'question' => 'Your DB username :',
                'user_input' => ''
            ],
            'DB_PASSWORD' => [
                'default' => '',
                'question' => 'Your DB password',
                'setHidden' => true,
                'user_input' => ''
            ],
            'DB_CHARSET' => [
                'default' => 'utf8',
                'question' => 'Your DB charset :',
                'user_input' => ''
            ],
            'DB_COLLATION' => [
                'default' => 'utf8_unicode_ci',
                'question' => 'Your DB collation :',
                'user_input' => ''
            ],
            'DB_PREFIX' => [
                'default' => '',
                'question' => 'Your DB prefix :',
                'user_input' => ''
            ],
            'MAILER_SMTP' => [
                'default' => 'smtp.mailtrap.io',
                'question' => 'Your SMTP server :',
                'user_input' => ''
            ],
            'MAILER_PORT' => [
                'default' => '25',
                'question' => 'Your SMTP server port :',
                'user_input' => ''
            ],
            'MAILER_USERNAME' => [
                'default' => '',
                'question' => 'Your SMTP server username :',
                'user_input' => ''
            ],
            'MAILER_PASSWORD' => [
                'default' => '',
                'question' => 'Your SMTP server password :',
                'setHidden' => true,
                'user_input' => ''
            ],
            'MAILER_ENCRYPTION' => [
                'default' => '',
                'question' => 'Your SMTP server encryption :',
                'user_input' => ''
            ],
            'MAILER_MAIL_FROM' => [
                'default' => 'postmaster@mysuperapp.local',
                'question' => 'Which "FROM" field for your emails ?',
                'user_input' => ''
            ],
            'MAILER_NAME_FROM' => [
                'default' => 'postmaster',
                'question' => 'Which "NAME" field for your emails ?',
                'user_input' => ''
            ],
        ];

    }

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    public static function composerPostInstall(Event $event)
    {
        $io = $event->getIO();
        $initializationCommand = new self();
        $envFileValues = $initializationCommand->envFileValues;
        $basePath = $initializationCommand->basePath;
        $colors = new Colors();

        echo "\n\n".$colors->getColoredString('Initialization', 'green')."\n";
        echo $colors->getColoredString('Welcome to the app initialization', 'brown')."\n";
        echo $colors->getColoredString('We will ask you a bunch of question in order to get you coding quicker', 'brown')."\n\n\n";

        foreach ($envFileValues as $keyEnv => &$envFileValue) {
            $question = $envFileValue['question'].'['.$colors->getColoredString($envFileValue['default'], 'black', 'cyan').'] : ';
            if (isset($envFileValue['setHidden'])) {
                $envFileValue['user_input'] = $io->askAndHideAnswer($question);

                if (!$envFileValue['user_input']) {
                    $envFileValue['user_input'] = $envFileValue['default'];
                }
            } else {
                $envFileValue['user_input'] = $io->ask($question, $envFileValue['default']);
            }

            if (stripos($envFileValue['user_input'], $basePath) !== false) {
                $envFileValue['user_input'] = str_replace($basePath, '${BASE_DIR}', $envFileValue['user_input']);
            }
            echo '  -> '.$colors->getColoredString($keyEnv, 'brown').'='.$colors->getColoredString($envFileValue['user_input'], 'green')."\n";
        }

        $envFileContent = '';
        foreach ($envFileValues as $keyEnv => $envValue) {
            $envFileContent .= $keyEnv.'='.$envValue['user_input']."\n";
        }

        file_put_contents($basePath.DIRECTORY_SEPARATOR.'.env', $envFileContent);

        echo "\n".$colors->getColoredString('*********************', 'brown')."\n";
        echo $colors->getColoredString('ASSETS COMPILATION', 'green')."\n";
        echo $colors->getColoredString('*********************', 'brown')."\n";

        // Execute assets dump
        passthru("php manager asset:compile --ansi");

        try {
            echo "\n".$colors->getColoredString('*********************', 'brown')."\n";
            echo $colors->getColoredString('MIGRATION RUN', 'green')."\n";
            echo $colors->getColoredString('*********************', 'brown')."\n";
            $dbh = new \PDO(
                'mysql:host='.getenv('DB_HOST').':'.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );


            // Run migration
            passthru("php manager migration:run up --ansi");

            // Run seeding
            passthru("php manager seed:run --ansi");

        } catch (\PDOException $ex) {
            $expLenght = strlen($ex->getMessage());
            $msg = 'UNABLE TO CONNECT TO YOUR DATABASE';
            $msgLenght = strlen($msg);
            $spaceLenght =  $expLenght - $msgLenght;

            echo $colors->getColoredString("\n".str_repeat('*', $expLenght), 'black', 'red');
            echo $colors->getColoredString("\n".$msg.str_repeat(' ', $spaceLenght), 'black', 'red');
            echo $colors->getColoredString("\n".$ex->getMessage(), 'black', 'red');
            echo $colors->getColoredString("\n".str_repeat('*', $expLenght), 'black', 'red');


            echo $colors->getColoredString("\n\nMind to change your .env file regarding DB", 'brown');
            echo $colors->getColoredString("\nThen execute consenquently ./manager migration:run up and ./manager seed:run", 'brown');
        }

        echo $colors->getColoredString("\n\nInitialization OK !\n", 'green');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        $formattedLine = $formatter->formatSection(
            'Initialization',
            "\n".'<comment>Welcome to the app initialization.'."\n".'We will ask you a bunch of question in order to get you coding quicker.</comment>'."\n"
        );
        $output->writeln("\n".$formattedLine."\n");

        $helper = $this->getHelper('question');

        foreach ($this->envFileValues as $keyEnv => &$envFileValue) {
            $question = new Question($envFileValue['question'].'[<question>'.$envFileValue['default'].'</question>] : ', $envFileValue['default']);
            if (isset($envFileValue['setHidden'])) {
                $question->setHidden(true);
            }
            $envFileValue['user_input'] = $helper->ask($input, $output, $question);
            if (stripos($envFileValue['user_input'], $this->basePath) !== false) {
                $envFileValue['user_input'] = str_replace($this->basePath, '${BASE_DIR}', $envFileValue['user_input']);
            }
            $output->writeln('  -> <comment>'.$keyEnv.'</comment>=<info>'.$envFileValue['user_input'].'</info>');
        }

        $envFileContent = '';
        foreach ($this->envFileValues as $keyEnv => $envValue) {
            $envFileContent .= $keyEnv.'='.$envValue['user_input']."\n";
        }

        file_put_contents($this->basePath.DIRECTORY_SEPARATOR.'.env', $envFileContent);

        $output->writeln('  -> <comment>'.$keyEnv.'</comment>=<info>'.$envFileValue['user_input'].'</info>');

        $output->writeln("\n<comment>*********************</comment>");
        $output->writeln("<info>ASSETS COMPILATION</info>");
        $output->writeln("<comment>*********************</comment>\n");

        // Execute assets dump
        passthru("php manager asset:compile --ansi");


        try {
            $output->writeln("\n<comment>*********************</comment>");
            $output->writeln("<info>MIGRATION RUN</info>");
            $output->writeln("<comment>*********************</comment>\n");
            $dbh = new \PDO(
                'mysql:host='.getenv('DB_HOST').':'.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );


            // Run migration
            passthru("php manager migration:run up --ansi");

            // Run seeding
            passthru("php manager seed:run --ansi");

        } catch (\PDOException $ex) {
            $expLenght = strlen($ex->getMessage());
            $msg = 'UNABLE TO CONNECT TO YOUR DATABASE';
            $msgLenght = strlen($msg);
            $spaceLenght =  $expLenght - $msgLenght;

            $output->writeln("\n<fg=black;bg=red>".str_repeat('*', $expLenght)."</>");
            $output->writeln("<fg=black;bg=red>UNABLE TO CONNECT TO YOUR DATABASE".str_repeat(' ', $spaceLenght)."</>");
            $output->writeln("<fg=black;bg=red>".$ex->getMessage()."</>");
            $output->writeln("<fg=black;bg=red>".str_repeat('*', $expLenght)."</>\n");


            $output->writeln("<comment>Mind to change your .env file regarding DB</comment>");
            $output->writeln("<comment>Then execute consenquently ./manager migration:run up and ./manager seed:run</comment>\n");
        }


        $output->writeln("<info>Initialization OK !</info>");

    }
}

class Colors {
    private $foreground_colors = array();
    private $background_colors = array();

    public function __construct() {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredString($string, $foreground_color = null, $background_color = null) {
        $colored_string = "";

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

    // Returns all foreground color names
    public function getForegroundColors() {
        return array_keys($this->foreground_colors);
    }

    // Returns all background color names
    public function getBackgroundColors() {
        return array_keys($this->background_colors);
    }
}
