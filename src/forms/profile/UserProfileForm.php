<?php

namespace Forms\Profile;

use Forms\Core\BaseForm;

class UserProfileForm extends BaseForm
{
    public function describe()
    {
        $this->form->setAction($this->router->pathFor('postUserProfile'));
        $this->form->setMethod('POST');

        $this->form->addText('firstname', $this->translator->trans('firstname'))
                    ->setRequired(true);

        $this->form->addText('lastname', $this->translator->trans('lastname'))
                    ->setRequired(true);

        $this->form->addEmail('email', $this->translator->trans('email'))
                    ->setRequired(true)
                    ->addRule($this->form::EMAIL, $this->translator->trans('not_valid_email'));

        $this->form->addPassword('password', $this->translator->trans('password'))
                    ->setRequired(true)
                    ->addRule($this->form::MIN_LENGTH, $this->translator->trans('min_length', [8]), 8);

        $this->form->addSubmit('save', $this->translator->trans('save'));

        return $this;
    }
}