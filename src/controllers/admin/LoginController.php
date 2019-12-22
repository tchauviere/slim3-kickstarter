<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    LoginController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers\Admin;

use Carbon\Carbon;
use Controllers\Core\BaseAdminController;
use Models\Recovery;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController extends BaseAdminController
{
    public function getLogin(Request $request, Response $response, $args) {
        $tplData = [];

        if ($request->getParam('reset_password')) {
            $tplData['reset_password'] = $request->getParam('reset_password');
        }
        if ($request->getParam('new_password')) {
            $tplData['new_password'] = $request->getParam('new_password');
        }
        if ($request->getParam('login')) {
            $tplData['login'] = $request->getParam('login');
        }

        return $this->twig->render($response, 'admin/login.twig', $tplData);
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

    public function postNewPassword(Request $request, Response $response, $args) {
        try {
            $token = $args['token'];
            $recovery = Recovery::where('token', $token)->firstOrFail();

            // Add five minutes in case user started recovery procedure at the limit expires date
            // and take a bit of time to fill out recovery form
            $now = Carbon::now()->addMinutes(5);

            if (strtotime($recovery->expires_at) < $now) {
                $recovery->forceDelete();
                return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                    'new_password' => 'false',
                ]));
            }

            $newPassword = $request->getParam('new_password');
            $newPasswordConfirm = $request->getParam('new_password_confirm');

            if ($newPassword !== $newPasswordConfirm) {
                return $response->withRedirect(
                    $this->container->get('router')->pathFor(
                        'getResetPassword',
                        [
                            'token' => $token
                        ],
                        [
                            'mismatch' => 'true',
                        ]
                    ));
            }

            $user = User::where('id', $recovery->user_id)->firstOrFail();
            $user->password = sha1($this->container->get('settings')['secret'].$newPassword);
            $user->saveOrFail();

            // Remove old recovery not usable anymore
            $recovery->forceDelete();

            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'new_password' => 'true',
            ]));
        } catch (\Exception $e) {
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'new_password' => 'false',
            ]));
        }
    }

    public function getResetPassword(Request $request, Response $response, $args) {
        try {
            $token = $args['token'];
            $recovery = Recovery::where('token', $token)->firstOrFail();
            $now = time();

            if (strtotime($recovery->expires_at) < $now) {
                $recovery->forceDelete();
                return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                    'new_password' => 'false',
                ]));
            }

            $tplData = [
                'token' => $token
            ];

            if ($request->getParam('mismatch')) {
                $tplData['mismatch'] = true;
            }

            return $this->twig->render($response, 'admin/reset_password.twig', $tplData);

        } catch (\Exception $e) {
            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'new_password' => 'false',
            ]));
        }


    }

    public function postResetPassword(Request $request, Response $response, $args) {
        try {
            $email = $request->getParam('fp_email');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                    'reset_password' => 'false',
                ]));
            }

            $user = User::where('email', $email)->firstOrFail();

            // Check if a recovery does not already exists for current user
            $recoveryExists = Recovery::where('user_id', $user->id)->first();

            if ($recoveryExists) {
                return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                    'reset_password' => 'false',
                ]));
            }

            $recovery = new Recovery();
            $recovery->user_id = $user->id;
            $recovery->token = sha1(time().$user->email.$this->container->get('settings')['secret']);
            $recovery->expires_at = Carbon::now()->addMinutes(30);
            $recovery->saveOrFail();

            $recoveryURL = $request->getUri()->getScheme().'://'.$request->getUri()->getHost();
            $recoveryURL .= $this->container->get('router')->pathFor(
                'getResetPassword',
                [
                    'token' => $recovery->token
                ],
                []
            );

            // Prepare email
            $passwordRecoveryEmail = $this->twig->fetch('emails/password_recovery.twig', [
                'user' => $user,
                'recovery_url' => $recoveryURL,
            ]);

            $this->mailer->setFrom(
                $this->container->get('settings')['mailer']['mail_from'],
                $this->container->get('settings')['mailer']['name_from']
            );
            $this->mailer->addAddress($user->email);
            $this->mailer->isHTML(true); // Set email format to HTML
            $this->mailer->Subject = $this->translator->trans('password_recovery_email_title');
            $this->mailer->Body    = $passwordRecoveryEmail;
            $this->mailer->send();

            return $response->withRedirect($this->container->get('router')->pathFor('getLogin', [], [
                'reset_password' => 'true',
            ]));
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
