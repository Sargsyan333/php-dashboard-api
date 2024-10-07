<?php

namespace Riconas\RiconasApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/nvt', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\NvtController::class . ':listAction');
    $group->post('', Controllers\NvtController::class . ':createOneAction');
    $group->delete('/{id}', Controllers\NvtController::class . ':deleteOneAction');
    $group->put('/{id}', Controllers\NvtController::class . ':updateOneAction');
});