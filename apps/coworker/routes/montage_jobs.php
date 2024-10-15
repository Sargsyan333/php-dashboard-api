<?php

namespace Riconas\RiconasApi\Coworker;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/montage-jobs', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\MontageJobController::class . ':listAction');
    $group->get('/{id}/hup', Controllers\HupController::class . ':getOneDetailsAction');
});