<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Slim\Http\ServerRequest;

class HupController extends BaseController
{
    private MontageJobRepository $montageJobRepository;

    public function __construct(
        MontageJobRepository $montageJobRepository
    ) {
        $this->montageJobRepository = $montageJobRepository;
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

        return $response->withJson($hupData, 200);
    }
}