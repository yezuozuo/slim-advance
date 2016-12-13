<?php

namespace App\Controllers;

use App\Models\Sdb;
/**
 * Class HomeController
 *
 * @package App\Controllers
 */
class HomeController extends Controller {
    public function index($request, $response) {
        $this->flash->addMessage('info', 'This is a message');
        $this->flash->addMessage('error', 'This is a message');

        $model = new Sdb();
        $model->sdb->select('*');
        $model->sdb->from('user');
        return $this->view->render($response, 'home.twig');
    }
}