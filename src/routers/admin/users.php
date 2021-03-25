<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    users.php
 * Date:    2021-03-25
 * Time:    18:17
 */

use Controllers\Admin\UsersController;
use Middlewares\Admin\AuthMiddleware;

$baseGeneratedAdminUrl = '/' . getenv('ADMIN_BASE_URI');

$app->group($baseGeneratedAdminUrl.'/users', function () use ($app) {

    /**
     * Display routes
     */
    $app->get('', UsersController::class . ':getUsers')->setName('getUsers');

    /**
     * Action routes
     */

})->add(new AuthMiddleware($app->getContainer()));
