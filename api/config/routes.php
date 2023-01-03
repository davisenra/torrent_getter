<?php

use App\Controllers\SearchController;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

return function (App $app) {
//    $app->options('/{routes:.+}', function ($request, $response) {
//        return $response;
//    });

    $app->post('/search', [SearchController::class, 'search']);

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request) {
        throw new HttpNotFoundException($request);
    });
};