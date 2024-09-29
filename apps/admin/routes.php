<?php

namespace Riconas\RiconasApi\Admin;

use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Routing\RouteCollectorProxy;

global $app;

$app->get('/', function (ServerRequest $request, Response $response) {
    return $response->withJson(['message' => 'Hello World']);
});

$app->group('/user', function (RouteCollectorProxy $group) {
    $group->post('/language', Controllers\UserController::class . ':setLanguageAction');
});

$app->group('/coworkers', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\CoworkerController::class . ':listAction');
    $group->post('', Controllers\CoworkerController::class . ':createOneAction');
    $group->delete('/{id}', Controllers\CoworkerController::class . ':deleteOneAction');
});