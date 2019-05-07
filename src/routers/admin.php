<?php
/**
 * Created by PhpStorm.
 * User: Thibaud
 * Date: 15/04/2019
 * Time: 12:20
 */

use Controllers\AdminController;
use Middlewares\AuthMiddleware;

$app->group('/dashboard', function () use ($app) {
   /*
    * Display routes
    */
    $app->get('', AdminController::class.':getAdmin')->setName('getAdmin');

   /*
    * Actions routes
    */
})->add(new AuthMiddleware($app->getContainer()));
