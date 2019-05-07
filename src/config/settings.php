<?php
return [
    'settings' => [

        // Slim 3 settings
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => true, // Allow the web server to send the content-length header
        'determineRouteBeforeAppMiddleware' => true, // Needed for middleware route name checking

        // App specific settings
        'mode' => 'dev', // Or 'prod'
        'secret' => '@CHANGE_THIS_SECRET@',
        'lang_path' =>  __DIR__ . '/../../lang',
        'uploadedFileDir' => __DIR__ . '/../../uploads',

        // Twig settings
        'twig' => [
            'tpl_path' => __DIR__ . '/../../templates',
            'cache_path' => __DIR__ . '/../../cache',
        ],

        // Monolog settings
       'logger' => [
           'name' => 'slim-app',
           'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../../logs/app.log',
           'level' => \Monolog\Logger::DEBUG,
       ],

        // DB settings
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => '',
            'username' => 'root',
            'password' => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];
