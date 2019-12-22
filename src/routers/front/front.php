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
use Controllers\Front\FrontController;
use Middlewares\Front\AuthMiddleware;

/*
 * Actions routes
 */
$app->group('/', function () use ($app) {

    $app->get('', FrontController::class . ':getHome')->setName('getHome');

    $app->get(getenv('ADMIN_BASE_URI'), function(Request $request, Response $response, $args) {
        if (isset($_SESSION['user'])) {
            return $response->withRedirect($this->get('router')->pathFor('getDashboard'));
        } else {
            return $response->withRedirect($this->get('router')->pathFor('getLogin'));
        }
    })->setName('getAdminRedirect');

})->add(new AuthMiddleware($app->getContainer()));



