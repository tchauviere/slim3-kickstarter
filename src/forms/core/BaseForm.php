<?php

namespace Forms\Core;

use Nette\Forms\Form;
use Psr\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Router;
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
     * App Router
     * @var Router
     */
    protected $router;

    /**
     * Translator
     * @var Translator
     */
    protected $translator;

    /**
     * @var Messages $flash
     */
    protected $flash;


    public function __construct()
    {
        $this->container = $GLOBALS['container'];
        $this->translator = $this->container->get('translator');
        $this->router = $this->container->get('router');
        $this->flash = $this->container->get('flash');

        $this->makeForm();

        return $this;
    }

    public function makeForm()
    {
        $this->form = new Form();
        $this->form->onRender[] = BaseForm::class.'::formRenderer';
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function formRenderer(Form $form)
    {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class="form-group row"';
        $renderer->wrappers['pair']['.error'] = 'has-danger';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-3 col-form-label"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';

        foreach ($form->form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-secondary');
                $usedPrimary = true;

            } elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
                $control->getControlPrototype()->addClass('form-control');

            } elseif ($type === 'file') {
                $control->getControlPrototype()->addClass('form-control-file');

            } elseif (in_array($type, ['checkbox', 'radio'], true)) {
                if ($control instanceof Nette\Forms\Controls\Checkbox) {
                    $control->getLabelPrototype()->addClass('form-check-label');
                } else {
                    $control->getItemLabelPrototype()->addClass('form-check-label');
                }
                $control->getControlPrototype()->addClass('form-check-input');
                $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
            }
        }

        return $form;
    }

    /**
     * Abstract method to describe fields related to form
     * @return BaseForm;
     */
    abstract public function describe();

    public function render()
    {
        return (string)$this->describe()->form;
    }

    public function isValid() {

        if (!$this->form->isSuccess()) {
            // Set errors in flash message directly
            $this->flash->addMessage('errors', [
                'title' => $this->translator->trans('error'),
                'msgs' => $this->getErrors(),
            ]);

            return false;
        }

        return true;
    }

    public function getValues()
    {
        return $this->form->getValues();
    }

    public function addError($message) {
        $this->form->addError($message);
    }

    /**
     * Return forms errors
     * @return array
     */
    public function getErrors()
    {
        return $this->form->getErrors();
    }

}