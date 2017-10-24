<?php

use Respect\Validation\Validator as v;

date_default_timezone_set('Asia/Shanghai');

session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/helpers.php';

$env = new Dotenv\Dotenv(__DIR__.'/../');
$env->load();

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'slim',
            'username' => 'root',
            'password' => '123456789',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''
        ]
	],
]);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function () use ($capsule) {
    return $capsule;
};

$container['flash'] = function() {
	return new \Slim\Flash\Messages;
};

$container['auth'] = function() {
    return new \App\Auth\Auth;
};

$container['view'] = function ($container) {
	$view = new \Slim\Views\Twig(__DIR__ . '/../resources/views/', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

    $view->getEnvironment()->addGlobal('auth',[
        'check' => $container->auth->check(),
        'user' => $container->auth->user()
    ]);

	$view->getEnvironment()->addGlobal('flash',$container->flash);

	return $view;
};

$container['validator'] = function() {
    return new App\Validation\Validator;
};


$container['AuthController'] = function($container) {
    return new \App\Controllers\Auth\AuthController($container);
};

$container['PasswordController'] = function($container) {
    return new \App\Controllers\Auth\PasswordController($container);
};

$container['csrf'] = function() {
    return new \Slim\Csrf\Guard;
};

$container['HomeController'] = function($container) {
	return new \App\Controllers\HomeController($container);
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container['csrf']);

v::with('App\\Validation\\Rules\\');

require __DIR__ . '/../app/routes.php';