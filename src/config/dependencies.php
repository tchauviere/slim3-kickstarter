<?php

// Slim
use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Http\Uri;
use Slim\Http\Environment;
use Slim\Views\TwigExtension;
use Slim\Flash\Messages;
use Slim\Container;
// SF
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
// Twig
use Twig\Extension\DebugExtension;
// Illuminate
use Illuminate\Database\Capsule\Manager;
// Monolog
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;

use PHPMailer\PHPMailer\PHPMailer;

$container = $app->getContainer();
// Remove default slim3 404 Handler
unset($container['notFoundHandler']);
// Remove default slim3 errorHandler
unset($container['errorHandler']);



// Twig template engine
$container['twig'] = function (Container $c) {
    $twig = new \Slim\Views\Twig($c->get('settings')['twig']['tpl_path'], [
        'cache' => $c->get('settings')['mode'] == 'dev' ? false : $c->get('settings')['twig']['cache_path'],
        'debug' => $c->get('settings')['mode'] == 'dev' ? true : false,
    ]);

    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en_US';

    // First param is the "default language" to use.
    $translator = new Translator($lang, null);
    // Set a fallback language incase you don't have a translation in the default language
    $translator->setFallbackLocales(['en_US']);
    // Add a loader that will get the php files we are going to store our translations in
    $translator->addLoader('php', new PhpFileLoader());

    // Add language files here
    $translator->addResource('php', $c->get('settings')['lang_path'].'/en_US.php', 'en_US'); // English
    $translator->addResource('php', $c->get('settings')['lang_path'].'/fr_FR.php', 'fr_FR'); // FR

    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $twig->addExtension(new TwigExtension($router, $uri));
    $twig->addExtension(new DebugExtension());
    $twig->addExtension(new TranslationExtension($translator));

    return $twig;
};

$container['mailer'] = function (Container $c) {
    $settings = $c->get('settings')['mailer'];

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Port = $settings['port'];
    $mail->SMTPAuth = true;
    // Sendgrid
    $mail->Username= $settings['username'];
    $mail->Password = $settings['password'];
    $mail->Host= $settings['smtp'];
    $mail->SMTPSecure = $settings['encryption'];
    $mail->CharSet = "UTF-8";

    return $mail;
};

$container['translator'] = function (Container $c) {
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en_US';

    // First param is the "default language" to use.
    $translator = new Translator($lang, null);
    // Set a fallback language incase you don't have a translation in the default language
    $translator->setFallbackLocales(['en_US']);
    // Add a loader that will get the php files we are going to store our translations in
    $translator->addLoader('php', new PhpFileLoader());

    // Add language files here
    $translator->addResource('php', $c->get('settings')['lang_path'].'/en_US.php', 'en_US'); // English
    $translator->addResource('php', $c->get('settings')['lang_path'].'/fr_FR.php', 'fr_FR'); // FR

    return $translator;
};

$container['eloquent'] = function(Container $c) {
    $capsule = new Manager();
    $capsule->addConnection($c->get('settings')['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['flash'] = function () {
    return new Messages();
};

$container['monolog'] = function (Container $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

$container['notFoundHandler'] = function (Container $c) {
    return function (Request $request, Response $response) use ($c) {
        $twig = $c->get('twig');
        return $twig->render($response->withStatus(404), 'core/404.twig', []);
    };
};

$container['errorHandler'] = function (Container $c) {

    return function (Request $request, Response $response, \Exception $exception) use ($c) {
        $logger = $c['monolog'];
        $twig = $c->get('twig');
        $finalTrace = [];

        $logger->addError('[CORE]ERROR HANDLER EXCEPTION : "'.$exception->getMessage().'" (CODE: "'.$exception->getCode().'")');
        $debugBacktrace = $exception->getTrace();


        foreach ($debugBacktrace as $trace) {
            $file = 'FILE => ';
            $line = 'LINE => ';
            $class = 'CLASS => ';
            $function = 'FUNCTION => ';

            if (isset($trace['file'])) {
                $file .= '<i>'.$trace['file'].'</i>';
            } else {
                $file .= '';
            }
            if (isset($trace['line'])) {
                $line .= '<b>'.$trace['line'].'</b>';
            } else {
                $line .= '';
            }
            if (isset($trace['class'])) {
                $class .= $trace['class'];
            } else {
                $class .= '';
            }
            if (isset($trace['function'])) {
                $function .= '<i>'.$trace['function'].'</i>';
            } else {
                $function .= '';
            }

            $finalTrace[] = '<li>'.$file.'<br/>'.$line.'<br/>'.$class.'<br/>'.$function.'<br/></li>';
        }


        return $response->write($exception->getMessage());

       /* return $twig->render($response->withStatus(500), 'core/500.twig', [
            'env' => getenv('SLIM3_MODE'),
            'exception' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ],
            'trace' => $finalTrace
        ]);*/
    };

};