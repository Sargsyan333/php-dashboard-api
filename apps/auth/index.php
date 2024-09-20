<?php

require_once __DIR__ . '/../../src/bootstrap.php';

use Riconas\RiconasApi\Integrations\Slim\Common\ApiErrorRenderer;
use DI\Bridge\Slim\Bridge;

$app = Bridge::create($container);

$app->add(
    new \Riconas\RiconasApi\Integrations\Slim\Middleware\Authentication(
        $container->get(\Riconas\RiconasApi\Authentication\AuthenticationService::class),
        $container,
    )
);

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware($_ENV['APP_DEBUG'] === "true", true, true);

// Error handler
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
//$errorHandler->setLogErrorRenderer($container->get(CustomLogRenderer::class));
$errorHandler->registerErrorRenderer('application/json', ApiErrorRenderer::class);

require_once 'routes.php';

$app->run();