<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    AuthMiddleware.php
 * Date:    15/04/2019
 * Time:    12:54
 */

namespace Middlewares\Front;

use Middlewares\Core\BaseMiddleware;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthMiddleware extends BaseMiddleware
{
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
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $this->twig->offsetSet('current_user', $user);
        }

        $response = $next($request, $response);

        return $response;
    }
}