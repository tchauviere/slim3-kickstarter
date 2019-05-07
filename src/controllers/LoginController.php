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

class LoginController extends BaseController
{
    public function getLogin(Request $request, Response $response, $args) {
        $tplData = [];

        $flashes = $this->container->flash->getMessages();

        if (isset($flashes['errors'])) {
            $tplData['errors'] = $flashes['errors'][0];
        }

        if ($request->getParam('action')) {
            $tplData['action'] = $request->getParam('action');
        }

        if ($request->getParam('login')) {
            $tplData['login'] = $request->getParam('login');
        }

        return $this->twig->render($response, 'login.twig', $tplData);
    }

    private function validateSignupRequest(Request $request) {
        $errors = [];
        $errors['title'] = $this->translator->trans('err_filled_form');
        $errors['msgs'] = [];

        if (!$request->getParam('s_firstname')) {
            $errors['msgs'][] = $this->translator->trans('err_firstname');
        }
        if (!$request->getParam('s_lastname')) {
            $errors['msgs'][] = $this->translator->trans('err_lastname');
        }
        if (!$request->getParam('s_email')) {
            $errors['msgs'][] = $this->translator->trans('err_email');
        }
        if (!$request->getParam('s_email_confirmation')) {
            $errors['msgs'][] = $this->translator->trans('err_confirm_email');
        }
        if ($request->getParam('s_email_confirmation') != $request->getParam('s_email')) {
            $errors['msgs'][] = $this->translator->trans('err_mismatch_email');
        }
        if (!$request->getParam('s_password')) {
            $errors['msgs'][] = $this->translator->trans('err_password');
        }
        if (!$request->getParam('s_password_confirmation')) {
            $errors['msgs'][] = $this->translator->trans('err_confirm_password');
        }
        if ($request->getParam('s_password') != $request->getParam('s_password_confirmation')) {
            $errors['msgs'][] = $this->translator->trans('err_mismatch_password');
        }

        $errors['form_data'] = $request->getParams();

        return $errors;
    }

    public function postSignup(Request $request, Response $response, $args) {
        // Check variables
        $errors = $this->validateSignupRequest($request);

        if (count($errors['msgs'])) {
            $this->container->flash->addMessage('errors', $errors);
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'action' => 'signup',
            ]));
        }

        try {
            // First check if User email already registered
            User::where([
                ['email', '=', $request->getParam('s_email')],
            ])->firstOrFail();

            $this->container->flash('errors', [
                'title' => $this->translator->trans('title_error'),
                'msg' => [$this->translator->trans('err_account_exists')],
                'form_data' => $request->getParams()
            ]);
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'action' => 'signup',
            ]));

        } catch (\Exception $e) {
            try {
                // If failed to find one match means we can go further in registration
                $user = new User();

                $user->firstname = $request->getParam('s_firstname');
                $user->lastname = $request->getParam('s_lastname');
                $user->email = $request->getParam('s_email');
                $user->password = sha1($request->getParam('s_password').$this->container->get('settings')['secret']);

                $user->saveOrFail();

                // Save user in session, then redirect to dashboard
                $this->setLoggedUser($user);

                return $response->withRedirect($this->container->get('router')->pathFor('getAdmin'));
            } catch (\Exception $e) {
                $this->container->flash->addMessage('errors', [
                    'title' => $this->translator->trans('title_error'),
                    'msgs' => [$this->translator->trans('err_signup_general')],
                    'form_data' => $request->getParams()
                ]);
                return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                    'action' => 'signup',
                ]));
            }
        }
    }

    public function postLogin(Request $request, Response $response, $args) {
        try {
            $user = User::where([
                ['email', '=', $request->getParam('l_email')],
                ['password', '=', sha1($this->container->get('settings')['secret'].$request->getParam('l_password'))],
            ])->firstOrFail();

            // Save user in session, then redirect to dashboard
            $this->setLoggedUser($user);

            return $response->withRedirect($this->container->get('router')->pathFor('getAdmin'));
        } catch (\Exception $e) {
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'login' => 'false',
            ]));
        }
    }

    public function getLogout(Request $request, Response $response, $args) {
        $this->unsetLoggedUser();
        return $response->withRedirect($this->container->get('router')->pathFor('getLogin'));
    }
}
