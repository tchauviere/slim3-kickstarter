<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    admin.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\Admin\HomeController;
use Middlewares\Admin\AuthMiddleware;

/*
 * Actions routes
 */
$baseGeneratedAdminUrl = '/' . getenv('ADMIN_BASE_URI');

$app->group($baseGeneratedAdminUrl, function () use ($app) {

    // Road meant to redirect Admin to Dashboard if logged in or to Auth form
    $app->get('', HomeController::class . ':getHome')->setName('getAdminRedirect');

})->add(new AuthMiddleware($app->getContainer()));






