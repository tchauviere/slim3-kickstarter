<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    login.php
 * Date:    15/04/2019
 * Time:    12:20
 */

use Controllers\LoginController;

/*
 * Display routes
 */
$app->get('/login', LoginController::class.':getLogin')->setName('getLogin');

/*
 * Actions routes
 */
$app->post('/login', LoginController::class.':postLogin')->setName('postLogin');
$app->post('/reset-password', LoginController::class.':postResetPassword')->setName('postResetPassword');
$app->get('/logout', LoginController::class.':getLogout')->setName('getLogout');
