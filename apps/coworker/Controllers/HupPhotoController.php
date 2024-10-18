<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageHupPhoto\HupPhotoState;
use Riconas\RiconasApi\Components\MontageHupPhoto\Service\MontageHupPhotoService;
use Riconas\RiconasApi\Components\MontageHupPhoto\Service\MontageHupPhotoStorageService;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;
use Slim\Psr7\UploadedFile;

class HupPhotoController extends BaseController
{
    private MontageJobRepository $montageJobRepository;
    private MontageHupPhotoStorageService $montageHupPhotoStorageService;
    private MontageHupPhotoService $montageHupPhotoService;

    public function __construct(
        MontageJobRepository $montageJobRepository,
        MontageHupPhotoStorageService $montageHupPhotoStorageService,
        MontageHupPhotoService $montageHupPhotoService
    ) {
        $this->montageJobRepository = $montageJobRepository;
        $this->montageHupPhotoStorageService = $montageHupPhotoStorageService;
        $this->montageHupPhotoService = $montageHupPhotoService;
    }

    /**
     * @throws OptimisticLockException
     * @throws RecordNotFoundException
     * @throws ORMException
     */
    public function deleteOneAction(ServerRequest $request, Response $response): Response
    {
        $jobId = $request->getAttribute('id');
        $job = $this->montageJobRepository->findById($jobId);
        if (is_null($job)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $hupPhotoId = $request->getAttribute('photoId');
        $this->montageHupPhotoService->deleteHupPhoto($hupPhotoId);

        return $response->withJson([], 204);
    }

    public function uploadAction(ServerRequest $request, Response $response): Response
    {
        $jobId = $request->getAttribute('id');
        $job = $this->montageJobRepository->findById($jobId);
        if (is_null($job)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $uploadedFiles = $request->getUploadedFiles();
        $photoState = $request->getParam('state');

        /** @var UploadedFile[] $uploadedPhotos */
        $uploadedPhotos = $uploadedFiles['files'];

        $photos = $this->montageHupPhotoService->insertPhotos(
            $job->getHup(),
            $uploadedPhotos,
            HupPhotoState::tryFrom($photoState)
        );
        $photosData = [];
        foreach ($photos as $photo) {
            $photosData[] = [
                'id' => $photo->getId(),
                'path' => $this->montageHupPhotoStorageService->getPhotoUrl($photo->getPhotoPath()),
            ];
        }

        return $response->withJson(['items' => $photosData], 200);
    }
}