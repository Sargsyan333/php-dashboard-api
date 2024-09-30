<?php

namespace Riconas\RiconasApi\Components\UserInvitation\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Components\UserInvitation\Repository\UserInvitationRepository;
use Riconas\RiconasApi\Components\UserInvitation\UserInvitation;
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

    public function createInvitation(User $user): void
    {
        $userInvitation = $this->userInvitationRepository->findByUserId($user->getId());
        if (false === is_null($userInvitation)) {
            return;
        }

        $userInvitation = new UserInvitation();
        $userInvitation
            ->setUserId($user->getId())
            ->setCode(StringUtility::generateRandomString(32));

        $this->entityManager->persist($userInvitation);
        $this->entityManager->flush();
    }
}
