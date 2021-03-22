<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    LoginController.php
 * Date:    15/04/2019
 * Time:    12:41
 */

namespace Controllers\Front;

use Controllers\Core\BaseFrontController;
use Forms\Profile\UserProfileForm;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class ProfileController extends BaseFrontController
{
    public function getUserProfile(Request $request, Response $response, $args) {
        try {
            $profileForm = new UserProfileForm();

            $user = User::where('id', $this->getLoggedUser()->id)
                        ->firstOrFail()
                        ->makeHidden(['password', 'role_id'])
                        ->toArray();


            $profileForm->setDefaultValues($user);
            $this->tpl_vars['form'] = $profileForm->render();
            return $this->twig->render($response, 'front/profile.twig', $this->tpl_vars);
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->addErrorMessage($this->translator->trans('unable_to_load_profile'));
            $this->persistMessages();
            return $response->withRedirect($this->router->pathFor('getHome'));
        }
    }

    public function postUserProfile(Request $request, Response $response, $args) {

        $this->persistMessages();
        return $response->withRedirect($this->router->pathFor('getUserProfile'));
    }
}
