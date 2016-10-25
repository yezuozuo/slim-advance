<?php

namespace App\Middleware;

/**
 * Class OldInputMiddleware
 *
 * @package App\Middleware
 */
class OldInputMiddleware extends Middleware {
    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $next
     * @return mixed
     */
    public function __invoke($request, $response, $next) {
        $this->container->view->getEnvironment()->addGlobal('old', isset($_SESSION['old']) ? $_SESSION['old'] : '');
        $_SESSION['old'] = $request->getParams();

        $response = $next($request, $response);

        return $response;

    }
}