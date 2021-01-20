<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    RegisterController.php
 * Date:    10/05/2019
 * Time:    14:41
 */

namespace Controllers\Front;

use Controllers\Core\BaseFrontController;
use Models\Role;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class RegisterController extends BaseFrontController
{
    public function getRegister(Request $request, Response $response, $args) {
        $tplData = [];

        $flashes = $this->container->flash->getMessages();

        if (isset($flashes['errors'])) {
            $tplData['errors'] = $flashes['errors'][0];
        }

        return $this->twig->render($response, 'front/register.twig', $tplData);
    }

    private function validateRegisterRequest(Request $request) {
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
        if (!$request->getParam('s_password_confirm')) {
            $errors['msgs'][] = $this->translator->trans('err_confirm_password');
        }
        if ($request->getParam('s_password') != $request->getParam('s_password_confirm')) {
            $errors['msgs'][] = $this->translator->trans('err_mismatch_password');
        }

        $errors['form_data'] = $request->getParams();

        return $errors;
    }

    public function postRegister(Request $request, Response $response, $args) {
        // Check variables
        $errors = $this->validateRegisterRequest($request);

        if (count($errors['msgs'])) {
            $this->container->flash->addMessage('errors', $errors);
            return $response->withRedirect($this->container->get('router')->pathFor('getRegister'));
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
            return $response->withRedirect($this->container->get('router')->pathFor('getRegister'));

        } catch (\Exception $e) {
            try {
                // If failed to find one match means we can go further in registration
                $user = new User();

                $user->firstname = $request->getParam('s_firstname');
                $user->lastname = $request->getParam('s_lastname');
                $user->email = $request->getParam('s_email');
                $user->password = sha1($this->container->get('settings')['secret'].$request->getParam('s_password'));
                $user->role_id = Role::where('name', 'user')->first()->id;

                $user->saveOrFail();

                $user->role  = User::where([
                                              ['email', '=', $user->email],
                                          ])->with('role')->firstOrFail();

                // Save user in session, then redirect to dashboard
                $this->setLoggedUser($user);

                return $response->withRedirect($this->container->get('router')->pathFor('getDashboard'));
            } catch (\Exception $e) {
                $this->container->flash->addMessage('errors', [
                    'title' => $this->translator->trans('title_error'),
                    'msgs' => [$this->translator->trans('err_signup_general')],
                    'form_data' => $request->getParams()
                ]);
                return $response->withRedirect($this->container->get('router')->pathFor('getRegister'));
            }
        }
    }
}