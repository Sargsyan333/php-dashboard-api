<?php

namespace Riconas\RiconasApi\Authentication;

use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Integrations\Firebase\Jwt\JwtEncoder;

class AuthenticationService
{
    private JwtEncoder $jwtEncoder;

    public function __construct(JwtEncoder $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    public function verifyUserPassword(string $currentPassword, string $plainTextPassword): bool
    {
        if (password_verify($plainTextPassword, $currentPassword)) {
            return true;
        }

        return false;
    }

    public function createAccessToken(User $user): string
    {
        return $this->jwtEncoder->encode(['user_email' => $user->getEmail()]);
    }
}
