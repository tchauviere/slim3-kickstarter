<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    login.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\Admin\LoginController;


$app->group('/'.getenv('ADMIN_BASE_URI'), function () use ($app) {
    /*
     * Display routes
     */
    $app->get('/login', LoginController::class.':getLogin')->setName('getLogin');
    $app->get('/reset-password/{token}', LoginController::class.':getResetPassword')->setName('getResetPassword');

    /*
     * Actions routes
     */
    $app->post('/login', LoginController::class.':postLogin')->setName('postLogin');
    $app->post('/reset-password', LoginController::class.':postResetPassword')->setName('postResetPassword');
    $app->post('/new-password/{token}', LoginController::class.':postNewPassword')->setName('postNewPassword');
    $app->get('/logout', LoginController::class.':getLogout')->setName('getLogout');
});

