<?php

namespace App\Middleware;

/**
 * Class GuestMiddleware
 *
 * @package App\Middleware
 */
class GuestMiddleware extends Middleware {
    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $next
     * @return mixed
     */
    public function __invoke($request, $response, $next) {
        if ($this->container->auth->check()) {
            return $response->withRedirect($this->container->router->pathFor('home'));
        }

        $response = $next($request, $response);

        return $response;

    }
}