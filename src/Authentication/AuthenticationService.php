<?php

namespace Riconas\RiconasApi\Authentication;

use Riconas\RiconasApi\Components\User\Repository\UserRepository;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Components\User\UserRole;
use Riconas\RiconasApi\Integrations\Firebase\Jwt\JwtDecoder;
use Riconas\RiconasApi\Integrations\Firebase\Jwt\JwtEncoder;

class AuthenticationService
{
    private JwtEncoder $jwtEncoder;
    private JwtDecoder $jwtDecoder;
    private UserRepository $userRepository;

    public function __construct(JwtEncoder $jwtEncoder, JwtDecoder $jwtDecoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->jwtDecoder = $jwtDecoder;
        $this->userRepository = $userRepository;
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

    public function getAuthenticatedUser(string $accessToken, UserRole $userRole): ?User
    {
        try {
            $accessTokenData = $this->jwtDecoder->decode($accessToken);
        } catch (\Exception) {
            return null;
        }

        if (false === array_key_exists('user_email', $accessTokenData)) {
            return null;
        }

        $user = $this->userRepository->findByEmailAndRole($accessTokenData['user_email'], $userRole);

        return $user;
    }
}
