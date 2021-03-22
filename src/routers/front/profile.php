<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    admin.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\Front\ProfileController;
use Middlewares\Front\AuthMiddleware;

/*
 * Actions routes
 */
$app->group('/profile', function () use ($app) {

    $app->get('', ProfileController::class . ':getUserProfile')->setName('getUserProfile');

    $app->post('/update', ProfileController::class . ':postUserProfile')->setName('postUserProfile');

})->add(new AuthMiddleware($app->getContainer()));



