<?php

namespace Riconas\RiconasApi\Components\MontageOntPhoto\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageOnt;
use Riconas\RiconasApi\Components\MontageOntPhoto\MontageOntPhoto;
use Riconas\RiconasApi\Components\MontageOntPhoto\Repository\MontageOntPhotoRepository;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Psr7\UploadedFile;

class MontageOntPhotoService
{
    private EntityManager $entityManager;
    private MontageOntPhotoRepository $montageOntPhotoRepository;
    private MontageOntPhotoStorageService $montageOntPhotoStorageService;

    public function __construct(
        EntityManager $entityManager,
        MontageOntPhotoRepository $montageOntPhotoRepository,
        MontageOntPhotoStorageService $montageOntPhotoStorageService
    ) {
        $this->entityManager = $entityManager;
        $this->montageOntPhotoRepository = $montageOntPhotoRepository;
        $this->montageOntPhotoStorageService = $montageOntPhotoStorageService;
    }

    /**
     * @throws OptimisticLockException
     * @throws RecordNotFoundException
     * @throws ORMException
     */
    public function deletePhoto(string $photoId): void
    {
        $ontPhoto = $this->montageOntPhotoRepository->getById($photoId);
        $this->montageOntPhotoStorageService->deletePhotoFile($ontPhoto->getPhotoPath());

        $this->entityManager->remove($ontPhoto);
        $this->entityManager->flush();
    }

    public function insertPhotos(MontageOnt $ont, array $uploadedPhotos): array
    {
        $montageOntPhotos = [];

        /** @var UploadedFile[] $uploadedPhotos */
        foreach ($uploadedPhotos as $uploadedPhoto) {
            $uploadedPhotoTargetPath = $this->montageOntPhotoStorageService->getPathForUploadedPhotoFile(
                $uploadedPhoto->getClientFilename()
            );

            $uploadedPhoto->moveTo($uploadedPhotoTargetPath);
            $uploadedPhotoTargetFileName = pathinfo($uploadedPhotoTargetPath, PATHINFO_BASENAME);

            $montageOntPhoto = new MontageOntPhoto();
            $montageOntPhoto
                ->setOnt($ont)
                ->setPhotoPath($uploadedPhotoTargetFileName);

            $this->entityManager->persist($montageOntPhoto);
            $this->entityManager->flush();

            $montageOntPhotos[] = $montageOntPhoto;
        }

        return $montageOntPhotos;
    }
}