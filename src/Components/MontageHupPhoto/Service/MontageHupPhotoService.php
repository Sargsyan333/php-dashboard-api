<?php

namespace Riconas\RiconasApi\Components\MontageHupPhoto\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Riconas\RiconasApi\Components\MontageHup\MontageHup;
use Riconas\RiconasApi\Components\MontageHupPhoto\HupPhotoState;
use Riconas\RiconasApi\Components\MontageHupPhoto\MontageHupPhoto;
use Riconas\RiconasApi\Components\MontageHupPhoto\Repository\MontageHupPhotoRepository;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Psr7\UploadedFile;

class MontageHupPhotoService
{
    private EntityManager $entityManager;
    private MontageHupPhotoRepository $montageHupPhotoRepository;
    private MontageHupPhotoStorageService $montageHupPhotoStorageService;

    public function __construct(
        EntityManager $entityManager,
        MontageHupPhotoRepository $montageHupPhotoRepository,
        MontageHupPhotoStorageService $montageHupPhotoStorageService
    ) {
        $this->entityManager = $entityManager;
        $this->montageHupPhotoRepository = $montageHupPhotoRepository;
        $this->montageHupPhotoStorageService = $montageHupPhotoStorageService;
    }

    /**
     * @throws OptimisticLockException
     * @throws RecordNotFoundException
     * @throws ORMException
     */
    public function deleteHupPhoto(string $hupPhotoId): void
    {
        $hupPhoto = $this->montageHupPhotoRepository->getById($hupPhotoId);
        $this->montageHupPhotoStorageService->deletePhotoFile($hupPhoto->getPhotoPath());

        $this->entityManager->remove($hupPhoto);
        $this->entityManager->flush();
    }

    public function insertPhotos(MontageHup $hup, array $uploadedPhotos, HupPhotoState $photoState): array
    {
        $montageHupPhotos = [];

        /** @var UploadedFile[] $uploadedPhotos */
        foreach ($uploadedPhotos as $uploadedPhoto) {
            $uploadedPhotoTargetPath = $this->montageHupPhotoStorageService->getPathForUploadedPhotoFile(
                $uploadedPhoto->getClientFilename()
            );

            $uploadedPhoto->moveTo($uploadedPhotoTargetPath);
            $uploadedPhotoTargetFileName = pathinfo($uploadedPhotoTargetPath, PATHINFO_BASENAME);

            $montageHupPhoto = new MontageHupPhoto();
            $montageHupPhoto
                ->setHupId($hup->getId())
                ->setState($photoState)
                ->setPhotoPath($uploadedPhotoTargetFileName);

            $this->entityManager->persist($montageHupPhoto);
            $this->entityManager->flush();

            $montageHupPhotos[] = $montageHupPhoto;
        }

        return $montageHupPhotos;
    }
}