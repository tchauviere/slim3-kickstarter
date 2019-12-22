<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    BaseMiddleware.php
 * Date:    15/04/2019
 * Time:    12:54
 */

namespace Middlewares\Core;

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Router;

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

    public function __construct(ContainerInterface $container) {
        $this->twig = $container->get('twig');
        $this->router = $container->get('router');
    }
}