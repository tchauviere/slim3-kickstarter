<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    LoginController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers\Admin;

use Controllers\Core\BaseAdminController;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UsersController extends BaseAdminController
{
    public function getUsers(Request $request, Response $response, $args) {
        return $this->twig->render($response, 'admin/users/list.twig', $this->tpl_vars);
    }

    public function getUsersPaging(Request $request, Response $response, $args) {
        $search = $request->getParam('search') ?: [];
        $start = (int)$request->getParam('start') ?: 0; // Default at least One page
        $length = (int)$request->getParam('length') ?: 10; // Default to 10 per page

        $collection = User::skip($start)->take($length)->toArray();

        return $response->withJson(['data' => $collection]);
    }



}
