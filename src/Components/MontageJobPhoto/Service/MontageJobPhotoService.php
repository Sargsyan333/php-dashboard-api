<?php

namespace Riconas\RiconasApi\Components\MontageJobPhoto\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Riconas\RiconasApi\Components\MontageJob\MontageJob;
use Riconas\RiconasApi\Components\MontageJobPhoto\MontageJobPhoto;
use Riconas\RiconasApi\Components\MontageJobPhoto\Repository\MontageJobPhotoRepository;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Psr7\UploadedFile;

class MontageJobPhotoService
{
    private EntityManager $entityManager;
    private MontageJobPhotoRepository $montageJobPhotoRepository;
    private MontageJobPhotoStorageService $montageJobPhotoStorageService;

    public function __construct(
        EntityManager $entityManager,
        MontageJobPhotoRepository $montageJobPhotoRepository,
        MontageJobPhotoStorageService $montageJobPhotoStorageService
    ) {
        $this->entityManager = $entityManager;
        $this->montageJobPhotoRepository = $montageJobPhotoRepository;
        $this->montageJobPhotoStorageService = $montageJobPhotoStorageService;
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

        $this->montageJobPhotoStorageService->deletePhotoFile($jobPhotoPath);

        $this->entityManager->remove($jobPhoto);
        $this->entityManager->flush();
    }

    public function insertPhotos(MontageJob $job, array $uploadedPhotos): array
    {
        $montageJobPhotos = [];

        /** @var UploadedFile[] $uploadedPhotos */
        foreach ($uploadedPhotos as $uploadedPhoto) {
            $uploadedPhotoTargetPath = $this->montageJobPhotoStorageService->getPathForUploadedPhotoFile(
                $uploadedPhoto->getClientFilename()
            );

            $uploadedPhoto->moveTo($uploadedPhotoTargetPath);
            $uploadedPhotoTargetFileName = pathinfo($uploadedPhotoTargetPath, PATHINFO_BASENAME);

            $montageJobPhoto = new MontageJobPhoto();
            $montageJobPhoto
                ->setJob($job)
                ->setPhotoPath($uploadedPhotoTargetFileName);

            $this->entityManager->persist($montageJobPhoto);
            $this->entityManager->flush();

            $montageJobPhotos[] = $montageJobPhoto;
        }

        return $montageJobPhotos;
    }
}