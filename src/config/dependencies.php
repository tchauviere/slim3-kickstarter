<?php

// Slim
use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Http\Uri;
use Slim\Http\Environment;
use Slim\Views\TwigExtension;
use Slim\Flash\Messages;
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

// Twig template engine
$container['twig'] = function ($c) {
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

$container['notFoundHandler'] = function ($c) {
    return function (Request $request, Response $response) use ($c) {
        $twig = $c->get('twig');
        return $twig->render($response->withStatus(404), 'core/404.twig', []);
    };
};

$container['mailer'] = function ($c) {
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

$container['translator'] = function ($c) {
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

$container['eloquent'] = function($c) {
    $capsule = new Manager();
    $capsule->addConnection($c->get('settings')['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['flash'] = function () {
    return new Messages();
};

$container['monolog'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));

    return $logger;
};
