<?php

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
    throw new Exception('Unable to find .env file !', -999);
}

// Instantiate the app
$settings = require __DIR__ . '/../src/config/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/config/dependencies.php';

// Register routes
require __DIR__ . '/../src/config/routes.php';

// Add Error Handler Middlewares at application level both for Dev and Prod
if (strtolower(getenv('SLIM3_MODE')) === 'dev') {
    $app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware($app));
}

// Run app
$app->run();

