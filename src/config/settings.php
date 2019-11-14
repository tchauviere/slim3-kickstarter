<?php
 /*
  * /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
  * Should not be touched unless we add a new variable entry to .env
  * and you want it to be accessible from any controller
  * /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
  */
return [
    'settings' => [

        // Slim 3 settings
        'displayErrorDetails' => (bool)(strtolower(getenv('SLIM3_MODE')) === 'dev'),
        'addContentLengthHeader' => (bool)(getenv('SLIM3_ROUTE_NAME_IN_MIDDLEWARE') === 'true'),
        'determineRouteBeforeAppMiddleware' => (bool)(getenv('SLIM3_ADD_CONTENT_LENGTH_HEADER') === 'true'),

        // App specific settings
        'mode' => strtolower(getenv('SLIM3_MODE')),
        'secret' => getenv('APP_SECRET'),
        'lang_path' => getenv('APP_LANG_PATH'),
        'uploadedFileDir' => getenv('APP_UPLOADED_FILE_DIRECTORY'),

        // Twig settings
        'twig' => [
            'tpl_path' => getenv('TWIG_TPL_PATH'),
            'cache_path' => getenv('TWIG_CACHE_PATH'),
        ],

        // Monolog settings
       'logger' => [
           'name' => getenv('MONOLOG_NAME'),
           'path' => getenv('MONOLOG_PATH'),
           'level' => constant(getenv('MONOLOG_LEVEL')),
       ],

        // DB settings
        'db' => [
            'driver' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'port' => (int)getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset'   => getenv('DB_CHARSET'),
            'collation' => getenv('DB_COLLATION'),
            'prefix'    => getenv('DB_PREFIX'),
        ],

        // Mailer : Check PHPMailer Documentation to see all option and don't forget to modify dependencies.php according to these change.
        'mailer' => [
            'smtp' => getenv('MAILER_SMTP'),
            'port' => (int)getenv('MAILER_PORT'),
            'username' => getenv('MAILER_USERNAME'),
            'password' => getenv('MAILER_PASSWORD'),
            'encryption' => getenv('MAILER_ENCRYPTION'),
            'mail_from' => getenv('MAILER_MAIL_FROM'),
            'name_from' => getenv('MAILER_NAME_FROM'),
        ],
    ],
];
