<?php

namespace Riconas\RiconasApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/user', function (RouteCollectorProxy $group) {
    $group->post('/language', Controllers\UserController::class . ':setLanguageAction');
});