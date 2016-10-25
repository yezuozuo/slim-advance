<?php

namespace App\Middleware;

/**
 * Class ValidationErrorsMiddleware
 *
 * @package App\Middleware
 */
class ValidationErrorsMiddleware extends Middleware {
    /**
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param                     $next
     * @return mixed
     */
    public function __invoke($request, $response, $next) {
        $this->container->view->getEnvironment()->addGlobal('errors', isset($_SESSION['errors']) ? $_SESSION['errors'] : '');
        unset($_SESSION['errors']);

        $response = $next($request, $response);

        return $response;

    }
}