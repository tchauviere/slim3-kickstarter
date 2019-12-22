<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    LoginController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers\Front;

use Controllers\Core\BaseFrontController;
use Slim\Http\Request;
use Slim\Http\Response;

class FrontController extends BaseFrontController
{
    public function getHome(Request $request, Response $response, $args) {
        $tplData = [];

        try {

        } catch (\Exception $e) {
           $this->logger->addError('FrontController::getHome "'.$e->getMessage().'" (CODE: "'.$e->getCode().'")');
        }

        return $this->twig->render($response, 'front/home.twig', $tplData);
    }
}
