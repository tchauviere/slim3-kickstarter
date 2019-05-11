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
use Slim\Http\UploadedFile;

class BaseController
{
    protected $container;
    protected $twig;
    protected $eloquent;
    protected $translator;
    protected $mailer;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->twig = $container->get('twig');
        $this->eloquent = $container->get('eloquent');
        $this->translator = $container->get('translator');
        $this->mailer = $container->get('mailer');
    }

    protected function getLoggedUser() {
        return @$_SESSION['user'];
    }

    protected function setLoggedUser(User $user) {
        $_SESSION['user'] = $user;
    }

    protected function unsetLoggedUser() {
        session_destroy();
    }

    protected function moveUploadedFile($directory, UploadedFile $uploadedFile) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public static function getQueries(Builder $builder) {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }
}
