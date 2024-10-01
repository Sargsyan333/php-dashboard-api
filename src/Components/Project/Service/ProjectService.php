<?php

namespace Riconas\RiconasApi\Components\Project\Service;
use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\Client\Repository\ClientRepository;
use Riconas\RiconasApi\Components\Project\Project;

class ProjectService
{
    private EntityManager $entityManager;

    private ClientRepository $clientRepository;

    public function __construct(EntityManager $entityManager, ClientRepository $clientRepository)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    public function createProject(string $name, string $code, string $clientId): void
    {
        $client = $this->clientRepository->getById($clientId);

        $project = new Project();
        $project
            ->setName($name)
            ->setCode($code)
            ->setClient($client)
        ;

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function updateProject(Project $project, string $newName, string $newCode, string $newClientId): void
    {
        $client = $this->clientRepository->getById($newClientId);

        $project
            ->setCode($newCode)
            ->setName($newName)
            ->setClient($client);

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function deleteProject(Project $project): void
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }
}