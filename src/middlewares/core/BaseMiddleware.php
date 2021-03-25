<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    BaseMiddleware.php
 * Date:    15/04/2019
 * Time:    12:54
 */

namespace Middlewares\Core;

use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Router;
use Symfony\Component\Translation\Translator;

class BaseMiddleware
{
    /**
     * @var Twig
     */
    protected $twig;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var Messages $flash
     */
    protected $flash;
    /**
     * @var Translator $translator
     */
    protected $translator;
    /**
     * @var Manager $eloquent
     */
    protected $eloquent;


    public function __construct(ContainerInterface $container) {

        $this->twig = $container->get('twig');
        $this->router = $container->get('router');
        $this->flash = $container->get('flash');
        $this->translator = $container->get('translator');
        $this->eloquent = $container->get('eloquent');
    }
}