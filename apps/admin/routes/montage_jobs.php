<?php

namespace Riconas\RiconasApi\Admin;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/montage-jobs', function (RouteCollectorProxy $group) {
    $group->post('', Controllers\MontageJobController::class . ':createOneAction');
    $group->get('', Controllers\MontageJobController::class . ':listAction');
    $group->post('/hub-file-upload', Controllers\MontageJobController::class . ':uploadHubFileAction');
    $group->delete('/{id}', Controllers\MontageJobController::class . ':deleteOneAction');
    $group->patch('/{id}/publish', Controllers\MontageJobController::class . ':publishOneAction');
    $group->patch('/{id}/unpublish', Controllers\MontageJobController::class . ':unpublishOneAction');
});