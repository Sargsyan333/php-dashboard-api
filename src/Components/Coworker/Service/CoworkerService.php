<?php

namespace Riconas\RiconasApi\Components\Coworker\Service;
use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\Coworker\Coworker;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Components\User\UserRole;
use Riconas\RiconasApi\Components\User\UserStatus;

class CoworkerService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createCoworker(string $companyName, string $emailAddress): void
    {
        $coworkerUser = new User();
        $coworkerUser
            ->setEmail($emailAddress)
            ->setRole(UserRole::ROLE_COWORKER)
            ->setStatus(UserStatus::STATUS_INACTIVE)
        ;

        $this->entityManager->persist($coworkerUser);
        $this->entityManager->flush();

        $coworker = new Coworker();
        $coworker
            ->setUser($coworkerUser)
            ->setCompanyName($companyName)
        ;

        $this->entityManager->persist($coworker);
        $this->entityManager->flush();
    }

    public function updateCoworker(Coworker $coworker, string $newCompanyName, string $newEmailAddress): void
    {
        $coworkerUser = $coworker->getUser();
        if ($coworkerUser->getEmail() !== $newEmailAddress) {
            $coworkerUser->setEmail($newEmailAddress);
            $coworkerUser->setStatus(UserStatus::STATUS_INACTIVE);
        }

        if ($coworker->getCompanyName() !== $newCompanyName) {
            $coworker->setCompanyName($newCompanyName);
        }

        $this->entityManager->persist($coworker);
        $this->entityManager->flush();
    }

    public function deleteCoworker(Coworker $coworker): void
    {
        $coworkerUser = $coworker->getUser();

        $this->entityManager->remove($coworkerUser);
        $this->entityManager->remove($coworker);
        $this->entityManager->flush();
    }
}