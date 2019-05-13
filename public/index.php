<?php
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
    $dotenv = \Dotenv\Dotenv::create(getenv('BASE_DIR'));
    $dotenv->load();

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
        var_dump($e->getTrace());
        exit;
    }
    // Force browser to display 500 Error
    throw new Exception('500');
}

