<?php

namespace Riconas\RiconasApi\Components\MontageJob\Service;

use Doctrine\ORM\EntityManager;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\MontageHup\Service\MontageHupService;
use Riconas\RiconasApi\Components\MontageJob\BuildingType;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCabelProperty\Service\MontageJobCabelPropertyService;
use Riconas\RiconasApi\Components\MontageJobOnt\Service\MontageJobOntService;
use Riconas\RiconasApi\Components\Nvt\Repository\NvtRepository;

class MontageJobService
{
    private EntityManager $entityManager;
    private MontageJobCabelPropertyService $montageJobCabelPropertyService;
    private MontageHupService $montageHupService;
    private MontageJobOntService $montageJobOntService;
    private MontageJobStorageService $storageService;
    private NvtRepository $nvtRepository;

    private CoworkerRepository $coworkerRepository;

    public function __construct(
        EntityManager $entityManager,
        MontageJobCabelPropertyService $montageJobCabelPropertyService,
        MontageHupService $montageHupService,
        MontageJobOntService $montageJobOntService,
        MontageJobStorageService $storageService,
        NvtRepository $nvtRepository,
        CoworkerRepository $coworkerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->montageJobCabelPropertyService = $montageJobCabelPropertyService;
        $this->montageHupService = $montageHupService;
        $this->montageJobOntService = $montageJobOntService;
        $this->storageService = $storageService;
        $this->nvtRepository = $nvtRepository;
        $this->coworkerRepository = $coworkerRepository;
    }

    public function createJob(
        string       $nvtId,
        string       $addressLine1,
        string       $addressLine2,
        BuildingType $buildingType,
        ?string      $coworkerId,
        ?string      $hbTmpFileName,
        array        $cabelData,
        array        $hupData,
        array        $ontData,
    ): void {
        $nvt = $this->nvtRepository->getById($nvtId);
        $coworker = $this->coworkerRepository->findById($coworkerId);

        $montageJob = new MontageJob();
        $montageJob
            ->setAddressLine1($addressLine1)
            ->setAddressLine2($addressLine2)
            ->setNvt($nvt)
            ->setBuildingType($buildingType)
            ->setCoworker($coworker)
        ;

        if (!empty($hbTmpFileName)) {
            $hbFileName = $this->storageService->storeTmpHbFile($hbTmpFileName);
            $montageJob->setHbFilePath($hbFileName);
        }

        $this->entityManager->persist($montageJob);
        $this->entityManager->flush();

        // Create cabel property record
        $this->montageJobCabelPropertyService->createProperty($montageJob, $cabelData);

        // Create montage hup
        $this->montageHupService->createHup($montageJob, $hupData);

        // Create ONTs
        if (count($ontData) > 0) {
            $this->montageJobOntService->createOnts($montageJob->getId(), $ontData);
        }
    }
}