<?php

namespace Riconas\RiconasApi\Components\PasswordResetRequest\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\PasswordResetRequest\PasswordResetRequest;
use Riconas\RiconasApi\Components\PasswordResetRequest\Repository\PasswordResetRequestRepository;
use Riconas\RiconasApi\Components\User\Service\UserService;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Components\UserPreference\Service\UserPreferenceService;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Riconas\RiconasApi\Mailing\MailingService;
use Riconas\RiconasApi\Utility\StringUtility;

class PasswordResetRequestService
{
    private const NEW_PASSWORD_REQUEST_ALLOW_TIME = 7200;

    private EntityManager $entityManager;

    private PasswordResetRequestRepository $passwordResetRequestRepository;

    private UserService $userService;

    private MailingService $mailingService;

    private UserPreferenceService $userPreferenceService;

    public function __construct(
        EntityManager $entityManager,
        PasswordResetRequestRepository $passwordResetRequestRepository,
        UserService $userService,
        MailingService $mailingService,
        UserPreferenceService $userPreferenceService
    ) {
        $this->entityManager = $entityManager;
        $this->passwordResetRequestRepository = $passwordResetRequestRepository;
        $this->userService = $userService;
        $this->mailingService = $mailingService;
        $this->userPreferenceService = $userPreferenceService;
    }

    public function requestPasswordReset(User $user): void
    {
        $previousPasswordResetRequest = $this->passwordResetRequestRepository->findByUserId($user->getId());
        if ($previousPasswordResetRequest) {
            $previousPasswordRequestTime = $previousPasswordResetRequest->getCreatedAt()->getTimestamp();
            if (time() - $previousPasswordRequestTime <= self::NEW_PASSWORD_REQUEST_ALLOW_TIME) {
                return;
            }

            // 2 hours have passed we can send a new recovery email
            $this->entityManager->remove($previousPasswordResetRequest);
        }

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest
            ->setUser($user)
            ->setCode(StringUtility::generateRandomString(32));

        $this->entityManager->persist($passwordResetRequest);
        $this->entityManager->flush();

        $userPreferenceLang = $this->userPreferenceService->getLanguagePreference($user->getId());
        $passwordResetLink = $this->buildResetPasswordLink($passwordResetRequest->getCode());
        $this->mailingService->sendPasswordRecoveryEmail($user->getEmail(), $userPreferenceLang, $passwordResetLink);
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

    private function buildResetPasswordLink(string $passwordResetRequestCode): string
    {
        return "{$_ENV['WEBSITE_DOMAIN']}/reset-password?code={$passwordResetRequestCode}";
    }
}