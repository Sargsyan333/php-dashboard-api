<?php

namespace Riconas\RiconasApi\Components\SubProject\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\SubProject\Subproject;

class SubprojectService
{
    private EntityManager $entityManager;

    private CoworkerRepository $coworkerRepository;

    public function __construct(
        EntityManager        $entityManager,
        CoworkerRepository   $coworkerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->coworkerRepository = $coworkerRepository;
    }

    public function createSubproject(string $code, string $projectId, string $coworkerId): void
    {
        $coworker = $this->coworkerRepository->findById($coworkerId);

        $subproject = new Subproject();
        $subproject
            ->setCode($code)
            ->setCoworker($coworker)
            ->setProjectId($projectId);
        ;

        $this->entityManager->persist($subproject);
        $this->entityManager->flush();
    }

    public function updateSubproject(
        Subproject $subproject,
        string     $newCode,
        string     $newProjectId,
        string     $newCoworkerId,
    ): void{
        $newCoworker = $this->coworkerRepository->findById($newCoworkerId);

        $subproject
            ->setCode($newCode)
            ->setProjectId($newProjectId)
            ->setCoworker($newCoworker);

        $this->entityManager->persist($subproject);
        $this->entityManager->flush();
    }

    public function deleteSubproject(Subproject $subproject): void
    {
        $this->entityManager->remove($subproject);
        $this->entityManager->flush();
    }
}
