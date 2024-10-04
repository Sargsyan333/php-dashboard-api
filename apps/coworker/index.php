<?php

namespace Riconas\RiconasApi\Coworker;

require_once __DIR__ . '/../../src/bootstrap.php';

use Riconas\RiconasApi\Auth\Controllers\BaseController;
use Riconas\RiconasApi\Integrations\Slim\Common\ApiErrorRenderer;
use DI\Bridge\Slim\Bridge;

$app = Bridge::create($container);

$app->add(
    new \Riconas\RiconasApi\Integrations\Slim\Middleware\Authentication(
        $container->get(\Riconas\RiconasApi\Authentication\AuthenticationService::class),
        BaseController::APP_NAME_COWORKER,
    ),
);

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware($_ENV['APP_DEBUG'] === "true", true, true);

// Error handler
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('application/json', ApiErrorRenderer::class);

require_once 'routes.php';

// Run app
$app->run();