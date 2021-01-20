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
use Slim\Http\Request;
use Slim\Http\Response;
use Models\Role;

class HomeController extends BaseAdminController
{
    public function getHome(Request $request, Response $response, $args) {

        if ($currentUser = $this->getLoggedUser()) {
            try {
                $userRole = Role::where('id', $_SESSION['user']->role_id)->firstOrFail();

                if (in_array($userRole->id, [Role::$ADMIN, Role::$SUPERADMIN])) {
                    // Redirect user to Dashboard
                    return $response->withRedirect($this->router->pathFor('getDashboard'));
                } else if ($userRole->id === Role::$USER) {
                    // Redirect user to Home
                    return $response->withRedirect($this->router->pathFor('getHome'));
                } else {
                    // User has no known role, something went wrong, empty session and redirect to login
                    $this->unsetLoggedUser();
                    throw new \Exception($this->translator->trans('access_denied'), __LINE__);
                }
            } catch (\Exception $e) {
                return $response->withRedirect($this->router->pathFor('getLogin'));
            }
        } else {
            // Redirect to login page because no user seems to be logged in
            return $response->withRedirect($this->router->pathFor('getLogin'));
        }
    }
}
