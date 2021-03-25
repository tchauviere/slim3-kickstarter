<?php
/**
 * Author:  tchauviere <thibaud.chauviere@gmail.com>
 * Project: slim3-kickstarter
 * File:    AuthMiddleware.php
 * Date:    15/04/2019
 * Time:    12:54
 */

namespace Middlewares\Core;

use Models\Recovery;
use Slim\Http\Request;
use Slim\Http\Response;

class RecoveryPasswordTokenMiddleware extends BaseMiddleware
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
        $route = $request->getAttribute('route');
        $arguments = $route->getArguments();
        $token = @$arguments['token'];

        // No token set redirect to auth without information to avoid hacking via brute force
        if (!$token) {
            $response = $response->withRedirect(
                $this->router->pathFor('getAuth', [], ['code' => __LINE__])
            );
        } else {
            // Check if token is valid and that current user is the owner
            $recovery = Recovery::where('token', $token)->first();
            $now = time();

            if (!$recovery) {
                // No recovery for given token, redirect without information to login page to avoid brute force hacking
                $response = $response->withRedirect($this->router->pathFor('getAuth', [], ['code' => __LINE__]));
            } else if (strtotime($recovery->expires_at) < $now) {
                // If recovery is expired redirect without information to login page to avoid brute force hacking
                // And delete recovery process from DB to clean it up.
                $recovery->forceDelete();
                $response = $response->withRedirect($this->router->pathFor('getAuth', [], ['code' => __LINE__]));
            } else {
                $response = $next($request, $response);
            }
        }

        return $response;
    }
}