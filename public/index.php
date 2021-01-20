<?php

use Middlewares\Core\BaseMiddleware;

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
        throw new Exception('Unable to find .env file !', -999);
    }

    // Instantiate the app
    $settings = require __DIR__ . '/../src/config/settings.php';
    $app = new \Slim\App($settings);

    // Set up dependencies
    require __DIR__ . '/../src/config/dependencies.php';

    // Register routes
    require __DIR__ . '/../src/config/routes.php';

    // Add Error Handler Middleware at application level
    $app->add(BaseMiddleware::class);
    // Run app
    $app->run();

} catch (Exception $e) {

    if (strtolower(getenv('SLIM3_MODE')) === 'dev') {
        echo '<h1>Mode Dev Error Display:</h1>';
        echo '<p>Message:</p>';
        echo '<pre>';
            var_dump($e->getMessage());
        echo '</pre>';
        echo '<p>Trace:</p>';
        echo '<pre>';
            var_dump($e->getTrace());
        echo '</pre>';
        echo '<pre>';

    }

    // Return 500 code
    http_response_code(500);
}

