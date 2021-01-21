<?php

namespace Forms\Auth;

use Forms\Core\BaseForm;

class AuthForm extends BaseForm
{
    protected function describe()
    {
        $this->form->addEmail('email', $this->translator->trans('email'));
        $this->form->addPassword('password', $this->translator->trans('password'));
        $this->form->addSubmit('login', $this->translator->trans('connect'));

        return $this;
    }
}