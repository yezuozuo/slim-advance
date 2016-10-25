<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AuthController
 *
 * @package App\Controllers\Auth
 */
class AuthController extends Controller {

    /**
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    public function getSignOut(Request $request, Response $response) {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getSignIn(Request $request, Response $response) {
        return $this->view->render($response, 'auth/signin.twig');
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    public function postSignIn(Request $request, Response $response) {
        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if (!$auth) {
            $this->flash->addMessage('error', 'Could not sign you in with those details');

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getSignUp(Request $request, Response $response) {
        return $this->view->render($response, 'auth/signup.twig');
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    public function postSignUp(Request $request, Response $response) {

        $validation = $this->validator->validate($request, [
            'email'    => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'name'     => v::noWhitespace()->notEmpty()->alpha(),
            'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        $user = User::create([
            'email'    => $request->getParam('email'),
            'name'     => $request->getParam('name'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        ]);

        $this->flash->addMessage('info', 'You have been signed up');

        $this->auth->attempt($user->email, $request->getParam('password'));

        return $response->withRedirect($this->router->pathFor('home'));
    }
}