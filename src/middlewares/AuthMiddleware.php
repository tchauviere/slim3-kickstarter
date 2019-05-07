<?php

namespace Middlewares;

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
        if (!isset($_SESSION['user'])) {
            // Unloggued user tries to access private section => Redirect to Login
            $response = $response->withRedirect(
                $this->router->pathFor('getLogin')
            );
        } else {
            // Logged user tries to access private section => Allow access
            $user = $_SESSION['user'];
            $this->twig->offsetSet('current_user', $user);
            $response = $next($request, $response);
        }

        return $response;
    }
}