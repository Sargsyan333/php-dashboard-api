<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJobOnt\Repository\MontageOntRepository;
use Riconas\RiconasApi\Components\MontageJobOnt\Service\MontageOntService;
use Slim\Http\ServerRequest;

class OntController extends BaseController
{
    private MontageOntRepository $montageOntRepository;
    private MontageOntService $montageOntService;

    public function __construct(
        MontageOntRepository $montageOntRepository,
        MontageOntService $montageOntService
    ) {
        $this->montageOntRepository = $montageOntRepository;
        $this->montageOntService = $montageOntService;
    }

    public function getOneDetailsAction(ServerRequest $request, Response $response): Response
    {
        $ontId = $request->getAttribute('id');
        $ont = $this->montageOntRepository->getById($ontId);

        $ontPhotosCount = $ont->getPhotos()->count();
        $ontPhotosData = [];
        if ($ontPhotosCount > 0) {
            $ontPhotosData = $ont->getPhotos()->getIterator()->toArray();
        }

        $ontData = [
            'id' => $ont->getId(),
            'code' => $ont->getCode(),
            'splitter_code' => $ont->getSplitterCode(),
            'splitter_fiber' => $ont->getSplitterFiber(),
            'odf_code_planned' => $ont->getOdfCodePlanned(),
            'odf_code' => $ont->getOdfCodeEdited(),
            'odf_pos_planned' => $ont->getOdfPosPlanned(),
            'odf_pos' => $ont->getOdfPosEdited(),
            'type' => $ont->getType(),
            'status' => $ont->getInstallationStatus()->value,
            'photos' => $ontPhotosData,
        ];

        return $response->withJson(['data' => $ontData], 200);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $ontType = $request->getParam('ont_type');
        $odfCode = $request->getParam('odf_code');
        $odfPosition = $request->getParam('odf_pos');
        $isPreInstalled = $request->getParam('is_pre_installed');
        $isInstalled = $request->getParam('is_installed');

        $ontId = $request->getAttribute('id');
        $ont = $this->montageOntRepository->getById($ontId);

        $this->montageOntService->updateOntCustomizableData(
            $ont,
            [
                'ont_type' => $ontType,
                'odf_code' => $odfCode,
                'odf_pos' => $odfPosition,
                'is_pre_installed' => $isPreInstalled,
                'is_installed' => $isInstalled,
            ],
        );

        return $response->withJson([], 204);
    }
}