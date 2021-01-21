<?php

namespace Forms\Core;

use Nette\Forms\Form;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;

abstract class BaseForm
{
    /**
     * Current Form
     * @var Form
     */
    protected $form;

    /**
     * App Dependencies Container
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Translator
     * @var Translator
     */
    protected $translator;

    public function __construct()
    {
        $this->container = $GLOBALS['container'];
        $this->translator = $this->container->get('translator');
        $this->form = new Form();

        return $this;
    }

    /**
     * Abstract method to describe fields related to form
     * @return BaseForm;
     */
    abstract protected function describe();

    public function render()
    {
        return (string)$this->describe()->form;
    }

}