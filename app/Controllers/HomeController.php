<?php

namespace App\Controllers;

/**
 * Class HomeController
 *
 * @package App\Controllers
 */
class HomeController extends Controller {
    public function index($request, $response) {
        $this->flash->addMessage('info', 'This is a message');
        $this->flash->addMessage('error', 'This is a message');

        return $this->view->render($response, 'home.twig');
    }
}