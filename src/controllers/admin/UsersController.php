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
        $this->tpl_vars['users'] = User::all();
        return $this->twig->render($response, 'admin/users/list.twig', $this->tpl_vars);
    }
}
