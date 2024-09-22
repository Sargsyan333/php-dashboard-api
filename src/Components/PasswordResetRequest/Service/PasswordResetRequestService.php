<?php

namespace Riconas\RiconasApi\Components\PasswordResetRequest\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\PasswordResetRequest\PasswordResetRequest;
use Riconas\RiconasApi\Components\PasswordResetRequest\Repository\PasswordResetRequestRepository;
use Riconas\RiconasApi\Components\User\Service\UserService;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Riconas\RiconasApi\Utility\StringUtility;

class PasswordResetRequestService
{
    private EntityManager $entityManager;

    private PasswordResetRequestRepository $passwordResetRequestRepository;

    private UserService $userService;

    public function __construct(
        EntityManager $entityManager,
        PasswordResetRequestRepository $passwordResetRequestRepository,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->passwordResetRequestRepository = $passwordResetRequestRepository;
        $this->userService = $userService;
    }

    public function requestPasswordReset(User $user): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest
            ->setUser($user)
            ->setCode(StringUtility::generateRandomString(32));

        $this->entityManager->persist($passwordResetRequest);
        $this->entityManager->flush();
    }

    public function resetUserPassword(string $passwordResetCode, string $newPlainPassword): void
    {
        $passwordResetRequest = $this->passwordResetRequestRepository->findByCode($passwordResetCode);
        if (is_null($passwordResetRequest)) {
            throw new RecordNotFoundException('Password reset request not found.');
        }

        $newPasswordHash = $this->userService->hashPassword($newPlainPassword);

        $user = $passwordResetRequest->getUser();
        $user->setPassword($newPasswordHash);

        $this->entityManager->persist($user);
        $this->entityManager->remove($passwordResetRequest);

        $this->entityManager->flush();
    }
}