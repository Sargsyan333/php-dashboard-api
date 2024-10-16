<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Slim\Http\ServerRequest;

class JobPhotoController extends BaseController
{
    private MontageJobRepository $montageJobRepository;

    public function __construct(MontageJobRepository $montageJobRepository)
    {
        $this->montageJobRepository = $montageJobRepository;
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
                'path' => $photo->getPath(),
            ];
        }

        return $response->withJson(['items' => $responseData], 200);
    }
}