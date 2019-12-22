<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

try {
    if (PHP_SAPI == 'cli-server') {
        // To help the built-in PHP dev server, check if the request was actually for
        // something which should probably be served as a static file
        $url  = parse_url($_SERVER['REQUEST_URI']);
        $file = __DIR__ . $url['path'];
        if (is_file($file)) {
            return false;
        }
    }

    require __DIR__ . '/../vendor/autoload.php';

    session_start();

    // Load environment
    putenv('BASE_DIR='.realpath(__DIR__.'/../'));
    putenv('ROUTER_DIR='.realpath(__DIR__.'/../src/routers/'));

    try {
        $dotenv = \Dotenv\Dotenv::create(getenv('BASE_DIR'));
        $dotenv->load();
    } catch (Exception $e) {
        throw new Exception('Unable to find .env file !', -999)
    }


    // Instantiate the app
    $settings = require __DIR__ . '/../src/config/settings.php';
    $app = new \Slim\App($settings);

    // Set up dependencies
    require __DIR__ . '/../src/config/dependencies.php';

    // Register routes
    require __DIR__ . '/../src/config/routes.php';

    // Run app
    $app->run();
} catch (Exception $e) {

    if (strtolower(getenv('SLIM3_MODE')) === 'dev') {
        var_dump($e->getMessage());
        var_dump($e->getTrace());
        exit;
    }

    // If error is concerning missing .env, we couldnot add log anyway because logger needs it to be set up.
    if ($e->getCode() != -999) {
        $settingLogger = $settings['logger'];
        $logger = new Logger($settingLogger['name']);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($settingLogger['path'], $settingLogger['level']));
        $logger->addError('INDEX.PHP (Entry Point) : "'.$e->getMessage().'" (CODE : "'.$e->getCode().'")');
    }

    // Force browser to display 500 Error
    throw new Exception('500');
}

