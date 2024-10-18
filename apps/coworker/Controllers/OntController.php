<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJobOnt\Repository\MontageJobOntRepository;
use Slim\Http\ServerRequest;

class OntController extends BaseController
{
    private MontageJobOntRepository $montageJobOntRepository;

    public function __construct(MontageJobOntRepository $montageJobOntRepository)
    {
        $this->montageJobOntRepository = $montageJobOntRepository;
    }

    public function getOneDetailsAction(ServerRequest $request, Response $response): Response
    {
        $ontId = $request->getAttribute('id');
        $ont = $this->montageJobOntRepository->getById($ontId);

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
            'installation_status' => $ont->getInstallationStatus()->value,
        ];

        return $response->withJson(['data' => $ontData], 200);
    }
}