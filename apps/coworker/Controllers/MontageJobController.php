<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Riconas\RiconasApi\Components\MontageJobCabelProperty\Service\MontageJobCabelPropertyService;
use Riconas\RiconasApi\Components\MontageJobComment\Service\MontageJobCommentService;
use Riconas\RiconasApi\Components\User\User;
use Slim\Http\ServerRequest;

class MontageJobController extends BaseController
{
    private MontageJobRepository $montageJobRepository;
    private CoworkerRepository $coworkerRepository;
    private MontageJobCabelPropertyService $montageJobCabelPropertyService;

    private MontageJobCommentService $montageJobCommentService;

    public function __construct(
        MontageJobRepository $montageJobRepository,
        CoworkerRepository $coworkerRepository,
        MontageJobCabelPropertyService $montageJobCabelPropertyService,
        MontageJobCommentService $montageJobCommentService
    ) {
        $this->montageJobRepository = $montageJobRepository;
        $this->coworkerRepository = $coworkerRepository;
        $this->montageJobCabelPropertyService = $montageJobCabelPropertyService;
        $this->montageJobCommentService = $montageJobCommentService;
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
                'cabel_type_planned' => $job['cabelTypePlanned'],
                'cabel_code' => $job['cabelCode'],
                'cabel_code_planned' => $job['cabelCodePlanned'],
                'tube_color' => $job['tubeColor'],
                'tube_color_planned' => $job['tubeColorPlanned'],
                'cabel_position' => $job['cabelPosition'],
                'cabel_length' => $job['cabelLength'],
                'disability_length' => $job['disabilityLength'],
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

    public function updateCabelPropsAction(ServerRequest $request, Response $response): Response
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

        $cabelLength = $request->getParam('cabel_length');
        $disabilityLength = $request->getParam('disability_length');
        if (
            (false === is_null($cabelLength) && false === is_numeric($cabelLength)) ||
            (false === is_null($disabilityLength) && false === is_numeric($disabilityLength))
        ) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $this->montageJobCabelPropertyService->updatePropertyCustomizableData(
            $job->getCabelProperty(),
            $request->getParams()
        );

        return $response->withJson([], 204);
    }

    public function commentAction(ServerRequest $request, Response $response): Response
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

        $commentText = $request->getParam('comment');
        if (is_null($commentText)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        /** @var User $authenticatedUser */
        $authenticatedUser = $request->getAttribute('AuthUser');
        $coworker = $this->coworkerRepository->getByUserId($authenticatedUser->getId());

        $this->montageJobCommentService->saveComment($job->getId(), $coworker->getId(), $commentText);

        return $response->withJson([], 204);
    }
}