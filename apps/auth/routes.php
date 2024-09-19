<?php

namespace Riconas\RiconasApi\Auth;

global $app;

$app->post('/auth', Controllers\AuthenticationController::class . ':authenticateAction');
