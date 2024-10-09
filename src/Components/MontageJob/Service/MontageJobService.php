<?php

namespace Riconas\RiconasApi\Components\MontageJob\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\MontageHup\Service\MontageHupService;
use Riconas\RiconasApi\Components\MontageJob\BuildingType;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCabelProperty\Service\MontageJobCabelPropertyService;
use Riconas\RiconasApi\Components\MontageJobOnt\Service\MontageJobOntService;

class MontageJobService
{
    private EntityManager $entityManager;
    private MontageJobCabelPropertyService $montageJobCabelPropertyService;
    private MontageHupService $montageHupService;
    private MontageJobOntService $montageJobOntService;

    public function __construct(
        EntityManager $entityManager,
        MontageJobCabelPropertyService $montageJobCabelPropertyService,
        MontageHupService $montageHupService,
        MontageJobOntService $montageJobOntService
    ) {
        $this->entityManager = $entityManager;
        $this->montageJobCabelPropertyService = $montageJobCabelPropertyService;
        $this->montageHupService = $montageHupService;
        $this->montageJobOntService = $montageJobOntService;
    }

    public function createJob(
        string $nvtId,
        string $addressLine1,
        string $addressLine2,
        BuildingType $buildingType,
        ?string $coworkerId,
        array $cabelData,
        array $hupData,
        array $ontData,
    ): void {
        $montageJob = new MontageJob();
        $montageJob
            ->setAddressLine1($addressLine1)
            ->setAddressLine2($addressLine2)
            ->setNvtId($nvtId)
            ->setBuildingType($buildingType)
            ->setCoworkerId($coworkerId)
        ;

        $this->entityManager->persist($montageJob);
        $this->entityManager->flush();

        // Create cabel property record
        $this->montageJobCabelPropertyService->createProperty($montageJob->getId(), $cabelData);

        // Create montage hup
        $this->montageHupService->createHup($montageJob->getId(), $hupData);

        // Create ONTs
        if (count($ontData) > 0) {
            $this->montageJobOntService->createOnts($montageJob->getId(), $ontData);
        }
    }
}