<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    admin.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Slim\Http\Request;
use Slim\Http\Response;
use Controllers\Front\HomeController;
use Middlewares\Front\AuthMiddleware;

/*
 * Actions routes
 */
$app->group('/', function () use ($app) {

    $app->get('', HomeController::class . ':getHome')->setName('getHome');

})->add(new AuthMiddleware($app->getContainer()));



