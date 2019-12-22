<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    register.php
 * Date:    10/05/2019
 * Time:    14:50
 */

use Controllers\Front\RegisterController;
use Middlewares\Front\AuthMiddleware;


$app->group('/', function () use ($app) {
    /*
     * Display routes
     */
    $app->get('register', RegisterController::class.':getRegister')->setName('getRegister');

    /*
     * Actions routes
     */
    $app->post('register', RegisterController::class.':postRegister')->setName('postRegister');
})->add(new AuthMiddleware($app->getContainer()));
