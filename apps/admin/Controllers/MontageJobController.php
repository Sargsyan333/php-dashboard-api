<?php

namespace Riconas\RiconasApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJob\BuildingType;
use Riconas\RiconasApi\Components\MontageJob\Service\MontageJobService;
use Slim\Http\ServerRequest;

class MontageJobController extends BaseController
{
    private MontageJobService $montageJobService;

    public function __construct(MontageJobService $montageJobService)
    {
        $this->montageJobService = $montageJobService;
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $nvtId = $request->getParam('nvt_id');
        $addressLine1 = $request->getParam('address_line1');
        $addressLine2 = $request->getParam('address_line2');
        $buildingType = $request->getParam('building_type');
        $coworkerId = $request->getParam('coworker_id');

        // Cabel properties
        $cabelType = $request->getParam('cabel_type');
        $cabelCode = $request->getParam('cabel_code');
        $tubeColor = $request->getParam('tube_color');

        // HUP properties
        $hupCode = $request->getParam('hup_code');
        $hupCustomerName = $request->getParam('hup_customer_name');
        $hupCustomerEmail = $request->getParam('hup_customer_email');
        $hupCustomerPhoneNumber1 = $request->getParam('hup_customer_phone_number1');
        $hupCustomerPhoneNumber2 = $request->getParam('hup_customer_phone_number2');

        $ontData = $request->getParam('ont');

        $this->montageJobService->createJob(
            $nvtId,
            $addressLine1,
            $addressLine2,
            BuildingType::from($buildingType),
            $coworkerId,
            [
                'type' => $cabelType,
                'code' => $cabelCode,
                'tube_color' => $tubeColor,
            ],
            [
                'code' => $hupCode,
                'customer_name' => $hupCustomerName,
                'customer_email' => $hupCustomerEmail,
                'customer_phone_number1' => $hupCustomerPhoneNumber1,
                'customer_phone_number2' => $hupCustomerPhoneNumber2,
            ],
            $ontData
        );

        return $response->withJson([], 201);
    }
}