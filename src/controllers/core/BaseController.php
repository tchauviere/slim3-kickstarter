<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    BaseController.php
 * Date:    15/04/2019
 * Time:    12:54
 */

namespace Controllers;

use Models\Connections;
use Models\User;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager;
use Symfony\Component\Translation\Translator;
use Slim\Http\UploadedFile;
use Slim\Views\Twig;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class BaseController
 * @package Controllers
 */
class BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var Twig
     */
    protected $twig;
    /**
     * @var Manager
     */
    protected $eloquent;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var PHPMailer
     */
    protected $mailer;

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
}
