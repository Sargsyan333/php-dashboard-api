<?php

namespace Riconas\RiconasApi\Components\UserInvitation\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Components\UserInvitation\Repository\UserInvitationRepository;
use Riconas\RiconasApi\Components\UserInvitation\UserInvitation;
use Riconas\RiconasApi\Components\UserInvitation\UserInvitationStatus;
use Riconas\RiconasApi\Utility\StringUtility;

class UserInvitationService
{
    private EntityManager $entityManager;
    private UserInvitationRepository $userInvitationRepository;

    public function __construct(EntityManager $entityManager, UserInvitationRepository $userInvitationRepository)
    {
        $this->entityManager = $entityManager;
        $this->userInvitationRepository = $userInvitationRepository;
    }

    public function createInvitation(User $user): string
    {
        $userInvitation = $this->userInvitationRepository->findByUserId($user->getId());
        if (false === is_null($userInvitation)) {
            if ($userInvitation->getVerifiedAt()) {
                throw new \RuntimeException('User has already accepted invitation.');
            }

            $userInvitation->setCode(StringUtility::generateRandomString(32));
        } else {
            $userInvitation = new UserInvitation();
            $userInvitation
                ->setUserId($user->getId())
                ->setCode(StringUtility::generateRandomString(32));
        }

        $this->entityManager->persist($userInvitation);
        $this->entityManager->flush();

        return $this->buildInvitationLink($userInvitation->getCode());
    }

    public function getInvitationStatus(string $userId): UserInvitationStatus
    {
        $userInvitation = $this->userInvitationRepository->findByUserId($userId);
        if (is_null($userInvitation)) {
            return UserInvitationStatus::NOT_SENT;
        }

        if (is_null($userInvitation->getVerifiedAt())) {
            return UserInvitationStatus::PENDING;
        }

        return UserInvitationStatus::ACCEPTED;
    }

    private function buildInvitationLink(string $invitationCode): string
    {
        return "{$_ENV['WEBSITE_DOMAIN']}/accept-invite?code={$invitationCode}";
    }
}
