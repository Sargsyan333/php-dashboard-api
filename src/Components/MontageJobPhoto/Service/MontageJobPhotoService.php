<?php

namespace Riconas\RiconasApi\Components\MontageJobPhoto\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Riconas\RiconasApi\Components\MontageJob\Service\MontageJobStorageService;
use Riconas\RiconasApi\Components\MontageJobPhoto\Repository\MontageJobPhotoRepository;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;

class MontageJobPhotoService
{
    private EntityManager $entityManager;
    private MontageJobPhotoRepository $montageJobPhotoRepository;
    private MontageJobStorageService $montageJobStorageService;

    public function __construct(
        EntityManager $entityManager,
        MontageJobPhotoRepository $montageJobPhotoRepository,
        MontageJobStorageService $montageJobStorageService
    ) {
        $this->entityManager = $entityManager;
        $this->montageJobPhotoRepository = $montageJobPhotoRepository;
        $this->montageJobStorageService = $montageJobStorageService;
    }

    /**
     * @throws OptimisticLockException
     * @throws RecordNotFoundException
     * @throws ORMException
     */
    public function deleteJobPhoto(string $jobPhotoId): void
    {
        $jobPhoto = $this->montageJobPhotoRepository->getById($jobPhotoId);
        $jobPhotoPath = $jobPhoto->getPhotoPath();

        $this->montageJobStorageService->deletePhotoFile($jobPhotoPath);

        $this->entityManager->remove($jobPhoto);
        $this->entityManager->flush();
    }
}