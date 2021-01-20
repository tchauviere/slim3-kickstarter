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
use Slim\Http\Request;
use Slim\Http\Response;
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

    /**
     * Example middleware invokable class
     *
     * @param Request $request PSR7 request
     * @param Response $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __invoke(Request $request, Response $response, $next)
    {
       try {
           $response = $next($request, $response);
           return $response;
       } catch (\Exception $appException) {
           throw $appException;
       }
    }
}