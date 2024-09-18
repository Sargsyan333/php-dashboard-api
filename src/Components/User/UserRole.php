<?php

namespace Riconas\RiconasApi\Components\User;

enum UserRole: string {
    case ROLE_ADMIN = 'ADMIN';
    case ROLE_COWORKER = 'COWORKER';
}