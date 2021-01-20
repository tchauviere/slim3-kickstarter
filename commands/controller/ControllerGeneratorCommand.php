<?php

namespace Commands\Controller;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Composer\Script\Event;

class ControllerGeneratorCommand extends Command
{
    protected $commandName = 'controller:make';
    protected $commandDescription = "Generate new controller for your project";

    protected $givenQuestions;
    protected $basePath;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->basePath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
        $this->givenQuestions = [
            'controllerName' => [
                'default' => time().'Controller',
                'question' => 'Name of your controller ? (camel case required)',
                'user_input' => ''
            ],
            'controllerPath' => [
                'default' => '',
                'question' => 'Where this controller should be generated ? (relative path from "controller" directory)',
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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        $formattedLine = $formatter->formatSection(
            'Controller',
            "\n".'<comment>Controller generator</comment>'."\n"
        );
        $output->writeln("\n".$formattedLine."\n");

        $helper = $this->getHelper('question');

        foreach ($this->givenQuestions as $keyQuestion => &$givenQuestion) {
            $question = new Question($givenQuestion['question'].'[<question>'.$givenQuestion['default'].'</question>] : ', $givenQuestion['default']);
            if (isset($givenQuestion['setHidden'])) {
                $question->setHidden(true);
            }

            if ($keyQuestion === 'controllerName') {

                $question->setValidator(function ($answer) {
                    if (!preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $answer)) {
                        throw new \RuntimeException(
                            'Not a valid controller name !'
                        );
                    }

                    return $answer;
                });
            }



            $givenQuestion['user_input'] = $helper->ask($input, $output, $question);
            /*if (stripos($envFileValue['user_input'], $this->basePath) !== false) {
                $envFileValue['user_input'] = str_replace($this->basePath, '${BASE_DIR}', $envFileValue['user_input']);
            }*/
            $output->writeln('  -> <info>'.$givenQuestion['user_input'].'</info>');
        }

        $controllerContent = '';

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
