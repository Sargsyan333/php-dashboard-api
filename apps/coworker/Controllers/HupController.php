<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageHup\Service\MontageHupService;
use Riconas\RiconasApi\Components\MontageHupPhoto\HupPhotoState;
use Riconas\RiconasApi\Components\MontageHupPhoto\Repository\MontageHupPhotoRepository;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Slim\Http\ServerRequest;

class HupController extends BaseController
{
    private MontageJobRepository $montageJobRepository;
    private MontageHupService $montageHupService;
    private MontageHupPhotoRepository $montageHupPhotoRepository;

    public function __construct(
        MontageJobRepository $montageJobRepository,
        MontageHupService $montageHupService,
        MontageHupPhotoRepository $montageHupPhotoRepository
    ) {
        $this->montageJobRepository = $montageJobRepository;
        $this->montageHupService = $montageHupService;
        $this->montageHupPhotoRepository = $montageHupPhotoRepository;
    }

    public function getOneDetailsAction(ServerRequest $request, Response $response): Response
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

        $hup = $job->getHup();

        $openedPhotos = $this->montageHupPhotoRepository->findAllByHupIdAndState(
            $hup->getId(),
            HupPhotoState::OPENED
        );

        $closedPhotos = $this->montageHupPhotoRepository->findAllByHupIdAndState(
            $hup->getId(),
            HupPhotoState::CLOSED
        );

        $hupData = [
            'id' => $hup->getId(),
            'hup_type' => $hup->getHupType(),
            'location' => $hup->getLocation(),
            'status' => $hup->getStatus(),
            'opened_photos' => array_map(
                function ($photo) {
                    return [
                        'id' => $photo->getId(),
                        'path' => $photo->getPath(),
                    ];
                },
                $openedPhotos
            ),
            'closed_photos' => array_map(
                function ($photo) {
                    return [
                        'id' => $photo->getId(),
                        'path' => $photo->getPath(),
                    ];
                },
                $closedPhotos
            ),
        ];

        return $response->withJson(['data' => $hupData], 200);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $hupType = $request->getParam('hup_type');
        $hupLocation = $request->getParam('location');
        $isPreInstalled = $request->getParam('is_pre_installed');
        $isInstalled = $request->getParam('is_installed');

        $jobId = $request->getAttribute('id');
        $job = $this->montageJobRepository->findById($jobId);
        if (is_null($job)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->montageHupService->updateHupDetails(
            $job->getHup(),
            [
                'hup_type' => $hupType,
                'location' => $hupLocation,
                'is_pre_installed' => $isPreInstalled,
                'is_installed' => $isInstalled,
            ],
        );

        return $response->withJson([], 204);
    }
}