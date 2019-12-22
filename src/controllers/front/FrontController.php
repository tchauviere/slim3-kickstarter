<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    LoginController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class FrontController extends BaseController
{
    public function getHome(Request $request, Response $response, $args) {
       try {
           $tplData = [];

           return $this->twig->render($response, 'front/home.twig', $tplData);
       } catch (\Exception $e) {
       }
    }
}
