<?php

use Respect\Validation\Validator as v;

date_default_timezone_set('Asia/Shanghai');

set_error_handler('zoco_auto_error_log', E_ALL & ~E_DEPRECATED & ~E_STRICT);
$GLOBALS['uuidToLog'] = uniqid('', true);
function zoco_auto_error_log($errorNo, $errorStr, $errorFile, $errorLine) {
    $curErrorNo = error_reporting();
    if (($curErrorNo & ~$errorNo) == $curErrorNo) {
        return true;
    }
    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $EXIT = false;
    switch ($errorNo) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $error_type = 'Notice';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $error_type = 'Warning';
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $error_type = 'Fatal Error';
            $EXIT = TRUE;
            break;
        default:
            $error_type = 'Fatal Error';
            $EXIT = TRUE;
            break;
    }
    $timezone = date_default_timezone_get();
    $momoid = isset($GLOBALS['momoidtolog']) ? $GLOBALS['momoidtolog'] : '';
    $requestUriText = $requestUri ? '   [REQUEST_URI:' . $requestUri . ']' : '   [REQUEST_URI: Unkown]';
    $text = '[' . date('d-M-Y H:i:s', time()) . ' ' . $timezone . '] ' . $momoid.'-'.$GLOBALS['uuidToLog'] . ' PHP' . ' ' . $error_type . ':  ' . $errorStr . ' in ' . $errorFile . ' on line ' . $errorLine . $requestUriText . "\n";
    $logPath = __DIR__.'/../log/'.date('Y-m-d').'.log';

    if(!file_exists($logPath)) {
        touch($logPath);
    }

    if (is_writeable($logPath)) {
        file_put_contents($logPath, $text, FILE_APPEND);
    }
    if ($EXIT) {
        die();
    }
    return true;
}

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