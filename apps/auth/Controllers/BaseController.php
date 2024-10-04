<?php

namespace Riconas\RiconasApi\Auth\Controllers;

use Riconas\RiconasApi\Components\User\UserRole;

abstract class BaseController
{
    protected const ERROR_INVALID_REQUEST_PARAMS = 'invalid_request_params';
    protected const ERROR_NOT_FOUND = 'not_found';

    protected const APP_NAME_ADMIN = 'admin';
    protected const APP_NAME_COWORKER = 'coworker';

    private const APP_NAMES = [
        self::APP_NAME_ADMIN,
        self::APP_NAME_COWORKER,
    ];

    protected const APP_NAME_USER_ROLE_MAP = [
        self::APP_NAME_ADMIN => UserRole::ROLE_ADMIN,
        self::APP_NAME_COWORKER => UserRole::ROLE_COWORKER,
    ];

    protected function validateAppName(string $appName): bool
    {
        return in_array($appName, self::APP_NAMES);
    }
}