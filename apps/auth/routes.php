<?php

namespace Riconas\RiconasApi\Auth;

global $app;

$app->post('/auth', Controllers\AuthenticationController::class . ':authenticateAction');
$app->post('/request-password-reset', Controllers\PasswordResetController::class . ':requestPasswordResetAction');
