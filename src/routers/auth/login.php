<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    login.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\Auth\LoginController;
use Controllers\Front\RegisterController;


$app->group('/auth', function () use ($app) {
    /*
     * Display routes
     */
    $app->get('/login', LoginController::class.':getLogin')->setName('getLogin');
    $app->get('/reset-password/{token}', LoginController::class.':getResetPassword')->setName('getResetPassword');

    $app->get('register', RegisterController::class.':getRegister')->setName('getRegister');

    /*
     * Actions routes
     */
    $app->post('/login', LoginController::class.':postLogin')->setName('postLogin');
    $app->get('/logout', LoginController::class.':getLogout')->setName('getLogout');

    $app->post('/reset-password', LoginController::class.':postResetPassword')->setName('postResetPassword');
    $app->post('/new-password/{token}', LoginController::class.':postNewPassword')->setName('postNewPassword');

    $app->post('register', RegisterController::class.':postRegister')->setName('postRegister');
});



