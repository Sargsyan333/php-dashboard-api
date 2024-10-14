<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Riconas\RiconasApi\Components\User\User;
use Slim\Http\ServerRequest;

class MontageJobController extends BaseController
{
    private MontageJobRepository $montageJobRepository;
    private CoworkerRepository $coworkerRepository;

    public function __construct(
        MontageJobRepository $montageJobRepository,
        CoworkerRepository $coworkerRepository
    ) {
        $this->montageJobRepository = $montageJobRepository;
        $this->coworkerRepository = $coworkerRepository;
    }

    public function listAction(ServerRequest $request, Response $response): Response
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = $request->getAttribute('AuthUser');
        $coworker = $this->coworkerRepository->getByUserId($authenticatedUser->getId());

        $projectId = $request->getParam('project_id');

        $page = $request->getParam('page', self::DEFAULT_PAGE_NUMBER);
        $perPage = $request->getParam('per_page', self::DEFAULT_PER_PAGE);

        $response = $this->validatePagingParams($page, $perPage, $response);
        if (400 === $response->getStatusCode()) {
            return $response;
        }

        if (!empty($projectId) && false === is_numeric($projectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $offset = ($page - self::MIN_PAGE_VALUE) * $perPage;
        $jobs = $this->montageJobRepository->getListByCoworkerId($coworker->getId(), $projectId, $offset, $perPage);
        $totalCount = $this->montageJobRepository->getTotalCountByCoworkerId($coworker->getId(), $projectId);

        $responseData = [];
        foreach ($jobs as $job) {
            $responseData[] = [
                'id' => $job['id'],
                'address_line1' => $job['addressLine1'],
                'address_line2' => $job['addressLine2'],
                'building_type' => $job['buildingType']->value,
                'hb_file_path' => $job['hbFilePath'],
                'nvt_code' => $job['nvtCode'],
                'subproject_code' => $job['subprojectCode'],
                'cabel_type' => $job['cabelType'],
                'cabel_code' => $job['cabelCode'],
                'tube_color' => $job['tubeColor'],
                'hup_code' => $job['hupCode'],
                'hup_status' => $job['hupStatus']->value,
                'hup_customer_name' => $job['hupCustomerName'],
                'hup_customer_email' => $job['hupCustomerEmail'],
                'hup_customer_phone_number1' => $job['hupCustomerPhoneNumber1'],
                'hup_customer_phone_number2' => $job['hupCustomerPhoneNumber2'],
            ];
        }

        return $response->withJson(
            [
                'items' => $responseData,
                'total_count' => $totalCount,
            ],
            200
        );
    }
}