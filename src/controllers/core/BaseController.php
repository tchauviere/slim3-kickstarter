<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    BaseController.php
 * Date:    15/04/2019
 * Time:    12:54
 */

namespace Controllers\Core;

use Forms\Core\BaseForm;
use Models\User;
use Nette\Forms\Form;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager;
use Slim\Router;
use Symfony\Component\Translation\Translator;
use Slim\Http\UploadedFile;
use Slim\Views\Twig;
use PHPMailer\PHPMailer\PHPMailer;
use Slim\Flash\Messages;
use Monolog\Logger;

/**
 * Class BaseController
 * @package Controllers
 */
class BaseController
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;
    /**
     * @var array $settings
     */
    protected $settings;
    /**
     * @var Twig $twig
     */
    protected $twig;
    /**
     * @var Router $router
     */
    protected $router;
    /**
     * @var Manager $eloquent
     */
    protected $eloquent;
    /**
     * @var Translator $translator
     */
    protected $translator;
    /**
     * @var PHPMailer $mailer
     */
    protected $mailer;
    /**
     * @var Messages $flash
     */
    protected $flash;
    /**
     * @var Logger $logger
     */
    protected $logger;

    /**
     * @var array $errors
     */
    private $errors = [];
    /**
     * @var array $success
     */
    private $success = [];
    /**
     * @var array $tpl_vars
     */
    protected $tpl_vars = [];


    /**
     * BaseController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->twig = $container->get('twig');
        $this->eloquent = $container->get('eloquent');
        $this->translator = $container->get('translator');
        $this->mailer = $container->get('mailer');
        $this->flash = $container->get('flash');
        $this->logger = $container->get('monolog');
        $this->router = $container->get('router');
        $this->settings = $container->get('settings');

        $flashes = $this->flash->getMessages();
        if (isset($flashes['errors'])) {
            $this->twig->offsetSet('errors', $flashes['errors'][0]);
        }
        if (isset($flashes['success'])) {
            $this->twig->offsetSet('success', $flashes['success'][0]);
        }

        $this->twig->offsetSet('active_menu', get_called_class());
    }

    /**
     * Add an error message in the "flash message" system
     *
     * @param string $message
     */
    protected function addErrorMessage($message) {
        $this->errors[] = $message;
    }

    /**
     * Add a success message in the "flash message" system
     *
     * @param string $message
     */
    protected function addSuccessMessage($message) {
        $this->success[] = $message;
    }

    /**
     * Return the number of errors in the flash message queue
     *
     * @return integer
     */
    protected function hasErrors() {
        return count($this->errors);
    }

    /**
     * Effectively add flash messages queue to flash system
     */
    protected function persistMessages() {
        if (count($this->errors)) {
            $this->flash->addMessage('errors', [
                'title' => $this->translator->trans('error'),
                'msgs' => $this->errors,
            ]);
        }

        if (count($this->success)) {
            $this->flash->addMessage('success', [
                'title' => $this->translator->trans('success'),
                'msgs' => $this->success,
            ]);
        }
    }

    /**
     * Get $_SESSION['user'] or false if none is found
     *
     * @return User|false
     */
    protected function getLoggedUser() {
        return @$_SESSION['user'];
    }

    /**
     * Set $_SESSION['user'] with our own $user object
     *
     * @param User $user
     */
    protected function setLoggedUser(User $user) {
        $_SESSION['user'] = $user;
    }

    /**
     * Delete current user session
     */
    protected function unsetLoggedUser() {
        session_destroy();
    }

    /**
     * Function used to move an uploaded to given $directory from $uploadedFile given
     *
     * @param $directory
     * @param UploadedFile $uploadedFile
     * @return string
     * @throws \Exception
     */
    protected function moveUploadedFile($directory, UploadedFile $uploadedFile) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    /**
     * Function used to dump an Eloquent's query with params quickly (usefull when debugging)
     *
     * @param Builder $builder
     * @return string
     */
    public static function getQueries(Builder $builder) {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }

    /**
     * @param $formType
     * @param array $data
     * @return BaseForm
     */
    public function loadForm($formType, array $data = []) {
        $form = new $formType($data);
        $form->describe();
        return $form;
    }
}
