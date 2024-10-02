<?php

namespace Riconas\RiconasApi\Components\SubProject\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\Project\Repository\ProjectRepository;
use Riconas\RiconasApi\Components\SubProject\Subproject;

class SubprojectService
{
    private EntityManager $entityManager;

    private CoworkerRepository $coworkerRepository;

    private ProjectRepository $projectRepository;

    public function __construct(
        EntityManager        $entityManager,
        CoworkerRepository   $coworkerRepository,
        ProjectRepository    $projectRepository
    ) {
        $this->entityManager = $entityManager;
        $this->coworkerRepository = $coworkerRepository;
        $this->projectRepository = $projectRepository;
    }

    public function createSubproject(string $code, string $projectId, string $coworkerId): void
    {
        $coworker = $this->coworkerRepository->findById($coworkerId);
        $project = $this->projectRepository->getById($projectId);

        $subproject = new Subproject();
        $subproject
            ->setCode($code)
            ->setCoworker($coworker)
            ->setProject($project);
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
        $newProject = $this->projectRepository->getById($newProjectId);

        $subproject
            ->setCode($newCode)
            ->setProject($newProject)
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
