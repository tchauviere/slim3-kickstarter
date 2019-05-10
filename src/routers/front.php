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

/*
 * Actions routes
 */
$app->group('/', function () use ($app) {

    $app->get('', function(Request $request, Response $response, $args) {
        if (isset($_SESSION['user'])) {
            return $response->withRedirect($this->get('router')->pathFor('getDashboard'));
        } else {
            return $response->withRedirect($this->get('router')->pathFor('getLogin'));
        }
    })->setName('getIndexRedirect');

});

