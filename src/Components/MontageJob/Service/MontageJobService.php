<?php

namespace Riconas\RiconasApi\Components\MontageJob\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\MontageHup\Service\MontageHupService;
use Riconas\RiconasApi\Components\MontageJob\BuildingType;
use Riconas\RiconasApi\Components\MontageJob\JobStatus;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobCabelProperty\Service\MontageJobCabelPropertyService;
use Riconas\RiconasApi\Components\MontageJobOnt\Service\MontageJobOntService;
use Riconas\RiconasApi\Components\Nvt\Repository\NvtRepository;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class MontageJobService
{
    private EntityManager $entityManager;
    private MontageJobCabelPropertyService $montageJobCabelPropertyService;
    private MontageHupService $montageHupService;
    private MontageJobOntService $montageJobOntService;
    private MontageJobStorageService $montageJobStorageService;
    private NvtRepository $nvtRepository;
    private CoworkerRepository $coworkerRepository;

    public function __construct(
        EntityManager                  $entityManager,
        MontageJobCabelPropertyService $montageJobCabelPropertyService,
        MontageHupService              $montageHupService,
        MontageJobOntService           $montageJobOntService,
        MontageJobStorageService       $montageJobStorageService,
        NvtRepository                  $nvtRepository,
        CoworkerRepository             $coworkerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->montageJobCabelPropertyService = $montageJobCabelPropertyService;
        $this->montageHupService = $montageHupService;
        $this->montageJobOntService = $montageJobOntService;
        $this->montageJobStorageService = $montageJobStorageService;
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

        $coworker = null;
        if (false === is_null($coworkerId)) {
            $coworker = $this->coworkerRepository->findById($coworkerId);
        }

        $montageJob = new MontageJob();
        $montageJob
            ->setAddressLine1($addressLine1)
            ->setAddressLine2($addressLine2)
            ->setNvt($nvt)
            ->setBuildingType($buildingType)
            ->setCoworker($coworker)
        ;

        if (!empty($hbTmpFileName)) {
            $hbFileName = $this->montageJobStorageService->storeTmpHbFile($hbTmpFileName);
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
            $this->montageJobOntService->createOnts($montageJob, $ontData);
        }
    }

    public function deleteJob(MontageJob $montageJob): void
    {
        $this->entityManager->remove($montageJob);
        $this->entityManager->flush();
    }

    public function publishJob(MontageJob $montageJob): void
    {
        $montageJob->setStatus(JobStatus::STATUS_PUBLISHED);

        $this->entityManager->persist($montageJob);
        $this->entityManager->flush();
    }

    public function unpublishJob(MontageJob $montageJob): void
    {
        $montageJob->setStatus(JobStatus::STATUS_DRAFT);

        $this->entityManager->persist($montageJob);
        $this->entityManager->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws RecordNotFoundException
     * @throws ORMException
     */
    public function updateJob(
        MontageJob   $montageJob,
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

        $coworker = null;
        if (false === is_null($coworkerId)) {
            $coworker = $this->coworkerRepository->findById($coworkerId);
        }

        $montageJob
            ->setAddressLine1($addressLine1)
            ->setAddressLine2($addressLine2)
            ->setNvt($nvt)
            ->setBuildingType($buildingType)
            ->setCoworker($coworker)
        ;

        if (!empty($hbTmpFileName)) {
            $hbFileName = $this->montageJobStorageService->storeTmpHbFile($hbTmpFileName);
            $montageJob->setHbFilePath($hbFileName);
        } else if (!empty($montageJob->getHbFilePath())) {
            $this->montageJobStorageService->deleteHbFile($montageJob->getHbFilePath());
            $montageJob->setHbFilePath(null);
        }

        $this->entityManager->persist($montageJob);
        $this->entityManager->flush();

        // Update cabel property data
        $this->montageJobCabelPropertyService->updateProperty($montageJob->getCabelProperty(), $cabelData);

        // Update montage hup
        $this->montageHupService->updateHup($montageJob->getHup(), $hupData);

        // Update ONTs
        $this->montageJobOntService->updateOnts($montageJob, $ontData);
    }
}