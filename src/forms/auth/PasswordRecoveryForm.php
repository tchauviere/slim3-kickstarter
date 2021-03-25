<?php

namespace Forms\Auth;

use Forms\Core\BaseForm;

class PasswordRecoveryForm extends BaseForm
{
    public function describe()
    {
        $passwordMinLength = (int)getenv('PASSWORD_MIN_LENGTH') ?: 8;

        $this->form->setAction($this->router->pathFor('postPasswordRecovery', [
            'token' => $this->data['token']
        ]));
        $this->form->setMethod('POST');

        $this->form->addPassword('password', $this->translator->trans('password'))
            ->setRequired(true)
            ->addRule($this->form::MIN_LENGTH, $this->translator->trans('min_length', [$passwordMinLength]), $passwordMinLength);

        $this->form->addPassword('password_verify', $this->translator->trans('password_verify'))
            ->setRequired(true)
            ->addRule($this->form::EQUAL,  $this->translator->trans('password_mismatch'), $this->form['password'])
            ->setOmitted();

        $this->form->addSubmit('forgotPassword', $this->translator->trans('retrieve_password'));

        return $this;
    }
}