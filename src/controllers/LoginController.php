<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    LoginController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers;

use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController extends BaseController
{
    public function getLogin(Request $request, Response $response, $args) {
        $tplData = [];

        if ($request->getParam('reset_password')) {
            $tplData['reset_password'] = $request->getParam('reset_password');
        }

        if ($request->getParam('login')) {
            $tplData['login'] = $request->getParam('login');
        }

        return $this->twig->render($response, 'login.twig', $tplData);
    }

    public function postLogin(Request $request, Response $response, $args) {
        try {
            $user = User::where([
                ['email', '=', $request->getParam('l_email')],
                ['password', '=', sha1($this->container->get('settings')['secret'].$request->getParam('l_password'))],
            ])->firstOrFail();

            // Save user in session, then redirect to dashboard
            $this->setLoggedUser($user);

            return $response->withRedirect($this->container->get('router')->pathFor('getDashboard'));
        } catch (\Exception $e) {
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'login' => 'false',
            ]));
        }
    }

    public function postResetPassword(Request $request, Response $response, $args) {
        try {
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin') [], [
                'reset_password' => 'true',
            ]);
        } catch (\Exception $e) {
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'reset_password' => 'false',
            ]));
        }
    }

    public function getLogout(Request $request, Response $response, $args) {
        $this->unsetLoggedUser();
        return $response->withRedirect($this->container->get('router')->pathFor('getLogin'));
    }
}
