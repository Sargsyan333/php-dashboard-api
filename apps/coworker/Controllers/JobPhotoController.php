<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Riconas\RiconasApi\Components\MontageJob\Service\MontageJobStorageService;
use Riconas\RiconasApi\Components\MontageJobPhoto\Service\MontageJobPhotoService;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;

class JobPhotoController extends BaseController
{
    private MontageJobRepository $montageJobRepository;
    private MontageJobStorageService $storageService;
    private MontageJobPhotoService $montageJobPhotoService;

    public function __construct(
        MontageJobRepository $montageJobRepository,
        MontageJobStorageService $storageService,
        MontageJobPhotoService $montageJobPhotoService
    ) {
        $this->montageJobRepository = $montageJobRepository;
        $this->storageService = $storageService;
        $this->montageJobPhotoService = $montageJobPhotoService;
    }

    public function getListAction(ServerRequest $request, Response $response): Response
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

        $jobPhotos = $job->getPhotos();
        $responseData = [];
        foreach ($jobPhotos as $photo) {
            $responseData[] = [
                'id' => $photo->getId(),
                'path' => $this->storageService->getPhotoUrl($photo->getPhotoPath()),
            ];
        }

        return $response->withJson(
            [
                'items' => $responseData,
                'total_count' => count($responseData),
            ],
            200
        );
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

        $jobPhotoId = $request->getAttribute('photoId');
        $this->montageJobPhotoService->deleteJobPhoto($jobPhotoId);

        return $response->withJson([], 204);
    }
}