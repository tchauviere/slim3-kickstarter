<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    login.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\Auth\AuthController;
use Controllers\Front\RegisterController;
use Middlewares\Core\RecoveryPasswordTokenMiddleware;

$app->group('/auth', function () use ($app) {
    /*
     * Display routes
     */
    $app->get('/login', AuthController::class.':getAuth')->setName('getAuth');
    $app->get('/forgot/password', AuthController::class.':getForgotPassword')->setName('getForgotPassword');
    $app->get('/password/recovery/{token}', AuthController::class.':getPasswordRecovery')
        ->add(new RecoveryPasswordTokenMiddleware($app->getContainer())) // Middleware to check given token validity and authorization
        ->setName('getPasswordRecovery');

    $app->get('register', RegisterController::class.':getRegister')->setName('getRegister');

    /*
     * Actions routes
     */
    $app->post('/login', AuthController::class.':postAuth')->setName('postAuth');
    $app->post('/forgot/password', AuthController::class.':postForgotPassword')->setName('postForgotPassword');
    $app->post('/password/recovery/{token}', AuthController::class.':postPasswordRecovery')
        ->add(new RecoveryPasswordTokenMiddleware($app->getContainer())) // Middleware to check given token validity and authorization
        ->setName('postPasswordRecovery');
    $app->get('/logout', AuthController::class.':getLogout')->setName('getLogout');


    $app->post('register', RegisterController::class.':postRegister')->setName('postRegister');
});



