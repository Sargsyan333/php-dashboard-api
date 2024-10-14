<?php

namespace Riconas\RiconasApi\Coworker;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/user', function (RouteCollectorProxy $group) {
    $group->post('/language', Controllers\UserController::class . ':setLanguageAction');
    $group->post('/change-password', Controllers\UserController::class . ':changePasswordAction');
    $group->get('/me', Controllers\UserController::class . ':getDetailsAction');
});

$app->group('/projects', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\ProjectController::class . ':getListAction');
});

require_once 'routes/montage_jobs.php';