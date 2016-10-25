<?php

namespace App\Controllers;

/**
 * Class Controller
 *
 * @property \Slim\Views\Twig                 $view
 * @property \Slim\Flash\Messages             $flash
 * @property \App\Validation\validator        $validator
 * @property \Slim\Interfaces\RouterInterface $router
 * @property \App\Auth\Auth                   $auth
 * @package App\Controllers
 */
class Controller {

    /**
     * @var
     */
    protected $container;

    /**
     * Controller constructor.
     *
     * @param $container
     */
    public function __construct($container) {
        $this->container = $container;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property) {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
}