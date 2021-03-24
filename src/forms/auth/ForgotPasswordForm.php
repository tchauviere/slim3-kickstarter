<?php

namespace Forms\Auth;

use Forms\Core\BaseForm;

class ForgotPasswordForm extends BaseForm
{
    public function describe()
    {
        $this->form->setAction($this->router->pathFor('postForgotPassword'));
        $this->form->setMethod('POST');

        $this->form->addEmail('email', $this->translator->trans('email'))
                    ->setRequired(true)
                    ->addRule($this->form::EMAIL, $this->translator->trans('not_valid_email'));

        $this->form->addSubmit('forgotPassword', $this->translator->trans('retrieve_password'));

        return $this;
    }
}