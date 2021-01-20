<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    admin.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\Admin\DashboardController;
use Middlewares\Admin\AuthMiddleware;

/*
 * Actions routes
 */
$baseGeneratedAdminUrl = '/' . getenv('ADMIN_BASE_URI');

$app->group($baseGeneratedAdminUrl.'/dashboard', function () use ($app) {
   /*
    * Display routes
    */
    $app->get('', DashboardController::class.':getDashboard')->setName('getDashboard');

   /*
    * Actions routes
    */
})->add(new AuthMiddleware($app->getContainer()));
