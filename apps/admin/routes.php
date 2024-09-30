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
    $group->put('/{id}', Controllers\CoworkerController::class . ':updateOneAction');
    $group->post('/{id}/invite', Controllers\CoworkerController::class . ':inviteOneAction');
});

$app->group('/clients', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\ClientController::class . ':listAction');
    $group->post('', Controllers\ClientController::class . ':createOneAction');
    $group->delete('/{id}', Controllers\ClientController::class . ':deleteOneAction');
    $group->put('/{id}', Controllers\ClientController::class . ':updateOneAction');
});