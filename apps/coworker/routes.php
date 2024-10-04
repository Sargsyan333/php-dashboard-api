<?php

namespace Riconas\RiconasApi\Coworker;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/user', function (RouteCollectorProxy $group) {
    $group->post('/language', Controller\UserController::class . ':setLanguageAction');
});
