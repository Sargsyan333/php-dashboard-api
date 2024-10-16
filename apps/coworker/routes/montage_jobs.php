<?php

namespace Riconas\RiconasApi\Coworker;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/montage-jobs', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\MontageJobController::class . ':listAction');
    $group->get('/{id}/hup', Controllers\HupController::class . ':getOneDetailsAction');
    $group->put('/{id}/hup', Controllers\HupController::class . ':updateOneAction');
    $group->put('/{id}/cabel-props', Controllers\MontageJobController::class . ':updateCabelPropsAction');
    $group->post('/{id}/comment', Controllers\MontageJobController::class . ':commentAction');
});