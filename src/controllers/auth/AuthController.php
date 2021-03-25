<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    LoginController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers\Auth;

use Carbon\Carbon;
use Controllers\Core\BaseAdminController;
use Forms\Auth\AuthForm;
use Forms\Auth\ForgotPasswordForm;
use Forms\Auth\PasswordRecoveryForm;
use Models\Recovery;
use Models\Role;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends BaseAdminController
{
    /**
     *
     * Form Login Page
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getAuth(Request $request, Response $response, $args) {
        $authForm = new AuthForm();
        $this->tpl_vars['form'] = $authForm->render();
        return $this->twig->render($response, 'auth/login.twig', $this->tpl_vars);
    }

    /**
     *
     * POST Login Form and sign-in
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function postAuth(Request $request, Response $response, $args) {
        $authForm = $this->loadForm(AuthForm::class);

        if ($authForm->isValid()) {
            $credentials = $authForm->getValues();

            $user = User::where([
                                    ['email', '=', $credentials->email],
                                    ['password', '=', sha1($this->settings['secret'].$credentials->password)],
                                ])->with('role')->first();

            if (!$user) {
                $this->addErrorMessage($this->translator->trans('bad_credentials'));
                $this->persistMessages();
                return $response->withRedirect($this->router->pathFor('getAuth'));
            }

            // Save user in session, then redirect to dashboard
            $this->setLoggedUser($user);

            // Redirect user depending on its role
            if (in_array($user->role_id, [Role::$ADMIN, Role::$SUPERADMIN])) {
                // Redirect user to Dashboard
                return $response->withRedirect($this->router->pathFor('getDashboard'));
            } else if ($user->role_id === Role::$USER) {
                // Redirect user to Home
                return $response->withRedirect($this->router->pathFor('getHome'));
            } else {
                // User has no known role, something went wrong, empty session and redirect to login
                $this->unsetLoggedUser();
                $this->addErrorMessage($this->translator->trans('bad_account_settings'));
                $this->persistMessages();
                return $response->withRedirect($this->router->pathFor('getAuth'));
            }
        } else {
            // Invalid Form
            return $response->withRedirect($this->router->pathFor('getAuth'));
        }
    }

    /**
     *
     * Form Forgot Password Page
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getForgotPassword(Request $request, Response $response, $args) {
        $forgotPasswordForm = new ForgotPasswordForm();
        $this->tpl_vars['form'] = $forgotPasswordForm->render();
        return $this->twig->render($response, 'auth/forgot_password.twig', $this->tpl_vars);
    }

    /**
     *
     * POST Forgot Password Form and send reset password email if user can start a recovery process
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Twig\Error\LoaderError
     */
    public function postForgotPassword(Request $request, Response $response, $args) {
        $forgotPasswordForm = $this->loadForm(ForgotPasswordForm::class);

        if ($forgotPasswordForm->isValid()) {
            $now = Carbon::now();
            $emailData = $forgotPasswordForm->getValues();
            $email = $emailData->email;
            $user = User::where('email', $email)->first();

            if ($user) {

                // Check if a recovery does not already exists for current user and that would be valid
                $recoveryExists = Recovery::where([
                                                    ['user_id', '=', $user->id],
                                                    ['expires_at', '>=', $now->format('Y-m-d H:i:s')]
                                                  ])->first();

                if (!$recoveryExists) {

                    // Create new recovery process
                    $recoveryToken = sha1(time().$user->email.$this->settings['secret']);
                    $recovery = new Recovery();
                    $recovery->user_id = $user->id;
                    $recovery->token = $recoveryToken;
                    $recovery->expires_at = $now->addMinutes((int)getenv('FORGOT_PASSWORD_TOKEN_EXPIRES_MIN') ?: 30)->format('Y-m-d H:i:s');
                    $recoverySaved = $recovery->save();

                    if ($recoverySaved) {
                        // Create recovery email
                        $recoveryURL = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
                        $recoveryURL .= $this->router->pathFor(
                            'getPasswordRecovery',
                            [
                                'token' => $recoveryToken
                            ],
                            []
                        );

                        // Prepare email
                        $passwordRecoveryEmail = $this->twig->fetch('emails/password_recovery.twig', [
                            'user'         => $user,
                            'recovery_url' => $recoveryURL,
                        ]);

                        $this->mailer->setFrom(
                            $this->settings['mailer']['mail_from'],
                            $this->settings['mailer']['name_from']
                        );
                        $this->mailer->addAddress($user->email);
                        $this->mailer->isHTML(true); // Set email format to HTML
                        $this->mailer->Subject = $this->translator->trans('password_recovery_email_title');
                        $this->mailer->Body = $passwordRecoveryEmail;
                        $isMailSent = $this->mailer->send();

                        if ($isMailSent) {
                            // All good
                            $this->addSuccessMessage($this->translator->trans('forgot_password_success'));
                            $this->persistMessages();
                            return $response->withRedirect($this->router->pathFor('getForgotPassword'));
                        } else {
                            // Cannot send mail delete recovery we created
                            Recovery::where('token', $recoveryToken)->forceDelete();
                            return $this->redirectErrorForgotPassword($response, __LINE__);
                        }
                    } else {
                        // Cannot create recovery process in DB
                        return $this->redirectErrorForgotPassword($response, __LINE__);
                    }
                } else {
                    // Recovery process already exists
                    return $this->redirectErrorForgotPassword($response, __LINE__);
                }
            } else {
                return $this->redirectErrorForgotPassword($response, __LINE__);
            }
        } else {
            // Invalid form sent
            return $response->withRedirect($this->router->pathFor('getForgotPassword'));
        }
    }

    /**
     *
     * Form Password Recovery Page
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function getPasswordRecovery(Request $request, Response $response, $args) {
        try {
            $passwordRecoveryForm = new PasswordRecoveryForm(['token' => $args['token']]);
            $this->tpl_vars['form'] = $passwordRecoveryForm->render();

            return $this->twig->render($response, 'auth/password_recovery.twig', $this->tpl_vars);
        } catch (\Exception $e) {
            // Unable to complete request, redirect to login page with no information
            return $response->withRedirect($this->router->pathFor('getAuth', [], ['code' => __LINE__]));
        }
    }

    /**
     *
     * POST Password Recovery Form, set new user password then set user as logged with its new data
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function postPasswordRecovery(Request $request, Response $response, $args) {
        $passwordRecoveryForm = $this->loadForm(PasswordRecoveryForm::class, ['token' => @$args['token']]);

        if ($passwordRecoveryForm->isValid()) {

            $newPassword = sha1($this->settings['secret'].$passwordRecoveryForm->getValues()->password);
            $userID = (int)Recovery::where('token', $args['token'])->first()->value('user_id');
            $isUpdated = User::where('id', $userID)->update([
                'password' => $newPassword,
            ]);
            $user = User::where('id', $userID)->with('role')->first();

            if ($isUpdated && $user) {
                // Save user in session, then redirect to right home depending on user role
                $this->setLoggedUser($user);

                // Redirect user depending on its role
                if (in_array($user->role_id, [Role::$ADMIN, Role::$SUPERADMIN])) {
                    // Redirect user to Dashboard
                    Recovery::where('token', $args['token'])->forceDelete();
                    return $response->withRedirect($this->router->pathFor('getDashboard'));
                } else if ($user->role_id === Role::$USER) {
                    // Redirect user to Home
                    Recovery::where('token', $args['token'])->forceDelete();
                    return $response->withRedirect($this->router->pathFor('getHome'));
                } else {
                    // User has no known role, something went wrong, empty session and redirect to login
                    $this->unsetLoggedUser();
                    $this->addErrorMessage($this->translator->trans('bad_account_settings'));
                    $this->persistMessages();
                    Recovery::where('token', $args['token'])->forceDelete();
                    return $response->withRedirect($this->router->pathFor('getAuth'));
                }
            } else {
                $this->addErrorMessage($this->translator->trans('unable_to_set_new_password'));
                $this->persistMessages();
                return $response->withRedirect($this->router->pathFor('getPasswordRecovery', ['token' => @$args['token']]));
            }
        } else {
            // Invalid Form
            return  $response->withRedirect($this->router->pathFor('getPasswordRecovery', ['token' => @$args['token']]));
        }
    }

    /**
     *
     * Simply logout any logged in user and redirect them to Auth Form Page
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function getLogout(Request $request, Response $response, $args) {
        $this->unsetLoggedUser();
        $this->addSuccessMessage($this->translator->trans('disconnect_success'));
        $this->persistMessages();
        return $response->withRedirect($this->router->pathFor('getAuth'));
    }

    //@TODO : To be moved and better thinked (generalist)
    private function redirectErrorForgotPassword(Response $response, $line = -42) {
        $line = (int)$line;
        $this->addErrorMessage($this->translator->trans('unable_to_retrieve_password')." (Code: $line)");
        $this->persistMessages();
        return $response->withRedirect($this->router->pathFor('getForgotPassword'));
    }
}
