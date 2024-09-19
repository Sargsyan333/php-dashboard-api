<?php

namespace Riconas\RiconasApi\Components\PasswordResetRequest\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\PasswordResetRequest\PasswordResetRequest;
use Riconas\RiconasApi\Utility\StringUtility;

class PasswordResetRequestService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function requestPasswordReset(string $userId): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest
            ->setUserId($userId)
            ->setCode(StringUtility::generateRandomString(32));

        $this->entityManager->persist($passwordResetRequest);
        $this->entityManager->flush();
    }
}