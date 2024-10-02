<?php

namespace Riconas\RiconasApi\Components\Nvt\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\Nvt\Nvt;
use Riconas\RiconasApi\Components\SubProject\Repository\SubprojectRepository;

class NvtService
{
    private EntityManager $entityManager;

    private CoworkerRepository $coworkerRepository;

    private SubprojectRepository $subprojectRepository;

    public function __construct(
        EntityManager $entityManager,
        CoworkerRepository $coworkerRepository,
        SubprojectRepository $subprojectRepository
    ) {
        $this->entityManager = $entityManager;
        $this->coworkerRepository = $coworkerRepository;
        $this->subprojectRepository = $subprojectRepository;
    }

    public function createNvt(string $code, string $subprojectId, ?string $coworkerId): void
    {
        $coworker = null;
        if (false === is_null($coworkerId)) {
            $coworker = $this->coworkerRepository->findById($coworkerId);
        }

        $subproject = $this->subprojectRepository->getById($subprojectId);

        $nvt = new Nvt();
        $nvt
            ->setCode($code)
            ->setCoworker($coworker)
            ->setSubproject($subproject);
        ;

        $this->entityManager->persist($nvt);
        $this->entityManager->flush();
    }

    public function updateNvt(
        Nvt     $nvt,
        string  $newCode,
        string  $newSubprojectId,
        ?string $newCoworkerId,
    ): void {
        $newSubproject = $this->subprojectRepository->getById($newSubprojectId);

        $newCoworker = null;
        if (false === is_null($newCoworkerId)) {
            $newCoworker = $this->coworkerRepository->findById($newCoworkerId);
        }

        $nvt
            ->setCode($newCode)
            ->setSubproject($newSubproject)
            ->setCoworker($newCoworker);

        $this->entityManager->persist($nvt);
        $this->entityManager->flush();
    }

    public function deleteNvt(Nvt $nvt): void
    {
        $this->entityManager->remove($nvt);
        $this->entityManager->flush();
    }
}