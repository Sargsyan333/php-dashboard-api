<?php

namespace Riconas\RiconasApi\Coworker;

use Slim\Routing\RouteCollectorProxy;

global $app;

$app->group('/montage-jobs', function (RouteCollectorProxy $group) {
    $group->get('', Controllers\MontageJobController::class . ':listAction');

    $group->get('/{id}/hup', Controllers\HupController::class . ':getOneDetailsAction');
    $group->put('/{id}/hup', Controllers\HupController::class . ':updateOneAction');

    $group->group('/{id}/hup/photos', function (RouteCollectorProxy $group) {
        $group->post('', Controllers\HupPhotoController::class . ':uploadAction');
        $group->delete('/{photoId}', Controllers\HupPhotoController::class . ':deleteOneAction');
    });

    $group->put('/{id}/cabel-props', Controllers\MontageJobController::class . ':updateCabelPropsAction');

    $group->post('/{id}/comment', Controllers\MontageJobController::class . ':commentAction');

    $group->group('/{id}/photos', function (RouteCollectorProxy $group) {
        $group->get('', Controllers\JobPhotoController::class . ':getListAction');
        $group->delete('/{photoId}', Controllers\JobPhotoController::class . ':deleteOneAction');
        $group->post('', Controllers\JobPhotoController::class . ':uploadAction');
    });

    $group->group('/ont', function (RouteCollectorProxy $group) {
        $group->get('/{id}', Controllers\OntController::class . ':getOneDetailsAction');
    });
});