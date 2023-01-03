<?php

use App\Middleware\CorsMiddleware;
use DI\Container;
use Slim\Factory\AppFactory;

$container = new Container();
$app = AppFactory::createFromContainer($container);

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, false);

$routes = require __DIR__ . '/routes.php';
$routes($app);

$app->add(new CorsMiddleware());

$app->run();