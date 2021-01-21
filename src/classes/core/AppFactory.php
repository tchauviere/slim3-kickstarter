<?php

namespace Classes\Core;

use Slim\App;

class AppFactory
{
    public static function create(array $settings)
    {
        return new App($settings);
    }
}