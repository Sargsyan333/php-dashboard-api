<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJobOnt\Repository\MontageOntRepository;
use Riconas\RiconasApi\Components\MontageJobOnt\Service\MontageOntService;
use Riconas\RiconasApi\Components\MontageOntPhoto\MontageOntPhoto;
use Riconas\RiconasApi\Components\MontageOntPhoto\Service\MontageOntPhotoStorageService;
use Slim\Http\ServerRequest;

class OntController extends BaseController
{
    private MontageOntRepository $montageOntRepository;
    private MontageOntService $montageOntService;
    private MontageOntPhotoStorageService $montageOntPhotoStorageService;

    public function __construct(
        MontageOntRepository $montageOntRepository,
        MontageOntService $montageOntService,
        MontageOntPhotoStorageService $montageOntPhotoStorageService
    ) {
        $this->montageOntRepository = $montageOntRepository;
        $this->montageOntService = $montageOntService;
        $this->montageOntPhotoStorageService = $montageOntPhotoStorageService;
    }

    public function getOneDetailsAction(ServerRequest $request, Response $response): Response
    {
        $ontId = $request->getAttribute('id');
        $ont = $this->montageOntRepository->getById($ontId);

        $ontPhotosData = [];
        $ontPhotos = $ont->getPhotos();
        /** @var MontageOntPhoto[] $ontPhotos */
        foreach ($ontPhotos as $ontPhoto) {
            $ontPhotosData[] = [
                'id' => $ontPhoto->getId(),
                'path' => $this->montageOntPhotoStorageService->getPhotoUrl($ontPhoto->getPhotoPath()),
            ];
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