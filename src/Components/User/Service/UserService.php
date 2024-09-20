<?php

namespace Riconas\RiconasApi\Components\User\Service;

class UserService
{
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
