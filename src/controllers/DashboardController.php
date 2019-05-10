<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    AdminController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers;

use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class DashboardController extends BaseAdminController
{
    public function getDashboard(Request $request, Response $response, $args) {
        $tplData = [];
        return $this->twig->render($response, 'admin/dashboard.twig', $tplData);
    }
}
