<?php

namespace Riconas\RiconasApi\Components\User;

enum UserStatus: string {
    case STATUS_ACTIVE = 'ACTIVE';
    case STATUS_INACTIVE = 'INACTIVE';
}