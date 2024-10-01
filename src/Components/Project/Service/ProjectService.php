<?php

namespace Riconas\RiconasApi\Components\Project\Service;
use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\Client\Repository\ClientRepository;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\Project\Project;

class ProjectService
{
    private EntityManager $entityManager;

    private ClientRepository $clientRepository;

    private CoworkerRepository $coworkerRepository;

    public function __construct(
        EntityManager $entityManager,
        ClientRepository $clientRepository,
        CoworkerRepository $coworkerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->coworkerRepository = $coworkerRepository;
    }

    public function createProject(string $name, string $code, string $clientId, string $coworkerId): void
    {
        $client = $this->clientRepository->getById($clientId);
        $coworker = $this->coworkerRepository->findById($coworkerId);

        $project = new Project();
        $project
            ->setName($name)
            ->setCode($code)
            ->setClient($client)
            ->setCoworker($coworker)
        ;

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function updateProject(
        Project $project,
        string $newName,
        string $newCode,
        string $newClientId,
        string $newCoworkerId,
    ): void{
        $newClient = $this->clientRepository->getById($newClientId);
        $newCoworker = $this->coworkerRepository->findById($newCoworkerId);

        $project
            ->setCode($newCode)
            ->setName($newName)
            ->setClient($newClient)
            ->setCoworker($newCoworker);

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function deleteProject(Project $project): void
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }
}