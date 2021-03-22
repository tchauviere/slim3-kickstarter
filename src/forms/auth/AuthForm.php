<?php

namespace Forms\Auth;

use Forms\Core\BaseForm;

class AuthForm extends BaseForm
{
    public function describe()
    {
        $this->form->setAction($this->router->pathFor('postLogin'));
        $this->form->setMethod('POST');

        $this->form->addEmail('email', $this->translator->trans('email'))
                    ->setRequired(true)
                    ->addRule($this->form::EMAIL, $this->translator->trans('not_valid_email'));

        $this->form->addPassword('password', $this->translator->trans('password'))
                    ->setRequired(true)
                    ->addRule($this->form::MIN_LENGTH, $this->translator->trans('min_length', [8]), 8);

        $this->form->addSubmit('login', $this->translator->trans('connect'));

        return $this;
    }
}