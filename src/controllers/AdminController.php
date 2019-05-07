<?php
/**
 * Created by PhpStorm.
 * User: Thibaud
 * Date: 15/04/2019
 * Time: 12:41
 */

namespace Controllers;

use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminController extends BaseAdminController
{
    public function getAdmin(Request $request, Response $response, $args) {
        $tplData = [];
        return $this->twig->render($response, 'admin/admin.twig', $tplData);
    }
}
