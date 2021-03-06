<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    admin.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\DashboardController;
use Middlewares\AuthMiddleware;

$app->group('/dashboard', function () use ($app) {
   /*
    * Display routes
    */
    $app->get('', DashboardController::class.':getDashboard')->setName('getDashboard');

   /*
    * Actions routes
    */
})->add(new AuthMiddleware($app->getContainer()));
