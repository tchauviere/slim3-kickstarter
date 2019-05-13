<?php

namespace Commands\Initialization;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\ProgressBar;

class InitializationCommand extends Command
{
    protected $commandName = 'project:init';
    protected $commandDescription = "Initialize easily your new project";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
        $formatter = $this->getHelper('formatter');

        $formattedLine = $formatter->formatSection(
            'Initialization',
            "\n".'<comment>Welcome to the app initialization.'."\n".'We will ask you a bunch of question in order to get you coding quicker.</comment>'."\n"
        );
        $output->writeln("\n".$formattedLine."\n");

        $envFileValues = [
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
                'default' => $basePath.DIRECTORY_SEPARATOR.'lang',
                'question' => 'Your language directory path :',
                'user_input' => ''
            ],
            'APP_UPLOADED_FILE_DIRECTORY' => [
                'default' => $basePath.DIRECTORY_SEPARATOR.'uploads',
                'question' => 'Your uploaded files directory path :',
                'user_input' => ''
            ],
            'TWIG_TPL_PATH' => [
                'default' => $basePath.DIRECTORY_SEPARATOR.'templates',
                'question' => 'Your twig templates path :',
                'user_input' => ''
            ],
            'TWIG_CACHE_PATH' => [
                'default' => $basePath.DIRECTORY_SEPARATOR.'cache',
                'question' => 'Your twig cache directory path :',
                'user_input' => ''
            ],
            'MONOLOG_NAME' => [
                'default' => 'app',
                'question' => 'Monolog app name :',
                'user_input' => ''
            ],
            'MONOLOG_PATH' => [
                'default' => $basePath.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'app.log',
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

        $helper = $this->getHelper('question');

        foreach ($envFileValues as $keyEnv => &$envFileValue) {
            $question = new Question($envFileValue['question'].'[<question>'.$envFileValue['default'].'</question>] : ', $envFileValue['default']);
            if (isset($envFileValue['setHidden'])) {
                $question->setHidden(true);
            }
            $envFileValue['user_input'] = $helper->ask($input, $output, $question);
            if (stripos($envFileValue['user_input'], $basePath) !== false) {
                $envFileValue['user_input'] = str_replace($basePath, '${BASE_DIR}', $envFileValue['user_input']);
            }
            $output->writeln('  -> <comment>'.$keyEnv.'</comment>=<info>'.$envFileValue['user_input'].'</info>');
        }

        $envFileContent = '';
        foreach ($envFileValues as $keyEnv => $envValue) {
            $envFileContent .= $keyEnv.'='.$envValue['user_input']."\n";
        }

        file_put_contents($basePath.DIRECTORY_SEPARATOR.'.env', $envFileContent);
    }
}
