<?php

namespace Riconas\RiconasApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/montage-jobs', function (RouteCollectorProxy $group) {
    $group->post('', Controllers\MontageJobController::class . ':createOneAction');
});