<?php

ini_set('display_errors', 'On');

use App\Controllers\HomeController;
use DI\Container;
use Selective\BasePath\BasePathDetector;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php';

$container = new Container();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addRoutingMiddleware();
$basePath = (new BasePathDetector($_SERVER))->getBasePath();
$app->setBasePath($basePath);
$app->addErrorMiddleware(true, true, true);

$container->set('view', function () {
    return Twig::create(__DIR__ . '/../resources/views', ['cache' => false]);
});

$container->set('HomeController', function () use ($container) {
    return new HomeController($container);
});

$app->get('/', [HomeController::class, 'index']);

$app->run();