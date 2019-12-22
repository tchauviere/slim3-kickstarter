<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    login.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\Front\LoginController;


$app->group('/login', function () use ($app) {
    /*
     * Display routes
     */
    $app->get('', LoginController::class.':getLogin')->setName('getFrontLogin');
    $app->get('/reset-password/{token}', LoginController::class.':getResetPassword')->setName('getFrontResetPassword');

    /*
     * Actions routes
     */
    $app->post('', LoginController::class.':postLogin')->setName('postFrontLogin');
    $app->post('/reset-password', LoginController::class.':postResetPassword')->setName('postFrontResetPassword');
    $app->post('/new-password/{token}', LoginController::class.':postNewPassword')->setName('postFrontNewPassword');
    $app->get('/logout', LoginController::class.':getLogout')->setName('getFrontLogout');
});

