<?php

$appSettings = include __DIR__.'/../../src/config/settings.php';

return [
    'paths'        => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/../migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/../seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database'        => 'db',
        'db'             => [
            'adapter' => $appSettings['settings']['db']['driver'],
            'host'    => $appSettings['settings']['db']['host'],
            'name'    => $appSettings['settings']['db']['database'],
            'user'    => $appSettings['settings']['db']['username'],
            'pass'    => $appSettings['settings']['db']['password'],
            'port'    => $appSettings['settings']['db']['port'],
            'charset' => $appSettings['settings']['db']['charset'],
        ],
    ],
];
