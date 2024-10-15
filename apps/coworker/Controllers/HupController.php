<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageHup\Service\MontageHupService;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Slim\Http\ServerRequest;

class HupController extends BaseController
{
    private MontageJobRepository $montageJobRepository;
    private MontageHupService $montageHupService;

    public function __construct(
        MontageJobRepository $montageJobRepository,
        MontageHupService $montageHupService
    ) {
        $this->montageJobRepository = $montageJobRepository;
        $this->montageHupService = $montageHupService;
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

        $hupData = [
            'id' => $hup->getId(),
            'hup_type' => $hup->getHupType(),
            'location' => $hup->getLocation(),
            'status' => $hup->getStatus(),
            'opened_hup_photo_path' => $hup->getOpenedHupPhotoPath(),
            'closed_hup_photo_path' => $hup->getClosedHupPhotoPath(),
        ];

        return $response->withJson(['data' => $hupData], 200);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $hupType = $request->getParam('hup_type');
        $hupLocation = $request->getParam('location');
        $isPreInstalled = $request->getParam('is_pre_installed');
        $isInstalled = $request->getParam('is_installed');
//        $openedHupPhotoPath = $request->getParam('opened_hup_photo_path');
//        $closedHupPhotoPath = $request->getParam('closed_hup_photo_path');

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