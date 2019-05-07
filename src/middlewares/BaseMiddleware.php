<?php

namespace Middlewares;

use Psr\Container\ContainerInterface;

class BaseMiddleware
{
    protected $twig;
    protected $router;

    public function __construct(ContainerInterface $container) {
        $this->twig = $container->get('twig');
        $this->router = $container->get('router');
    }
}