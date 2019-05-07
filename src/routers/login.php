<?php
/**
 * Created by PhpStorm.
 * User: Thibaud
 * Date: 15/04/2019
 * Time: 12:20
 */

use Controllers\LoginController;

/*
 * Display routes
 */
$app->get('/login', LoginController::class.':getLogin')->setName('getLogin');

/*
 * Actions routes
 */
$app->post('/signup', LoginController::class.':postSignup')->setName('postSignup');
$app->post('/login', LoginController::class.':postLogin')->setName('postLogin');
$app->get('/logout', LoginController::class.':getLogout')->setName('getLogout');
