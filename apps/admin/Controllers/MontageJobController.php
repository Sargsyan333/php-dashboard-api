<?php

namespace Riconas\RiconasApi\Admin\Controllers;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\MontageJob\BuildingType;
use Riconas\RiconasApi\Components\MontageJob\Repository\MontageJobRepository;
use Riconas\RiconasApi\Components\MontageJob\Service\MontageJobService;
use Riconas\RiconasApi\Components\MontageJobOnt\MontageJobOnt;
use Riconas\RiconasApi\Components\MontageJobOnt\OntActivity;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Riconas\RiconasApi\Storage\StorageService;
use Slim\Http\ServerRequest;
use Slim\Psr7\UploadedFile;

class MontageJobController extends BaseController
{
    private MontageJobService $montageJobService;

    private StorageService $storageService;

    private MontageJobRepository $montageJobRepository;

    public function __construct(
        MontageJobService $montageJobService,
        StorageService $storageService,
        MontageJobRepository $montageJobRepository
    ) {
        $this->montageJobService = $montageJobService;
        $this->storageService = $storageService;
        $this->montageJobRepository = $montageJobRepository;
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $nvtId = $request->getParam('nvt_id');
        $addressLine1 = $request->getParam('address_line1');
        $addressLine2 = $request->getParam('address_line2');
        $buildingType = $request->getParam('building_type');
        $coworkerId = $request->getParam('coworker_id');
        $hbFile = $request->getParam('hb_file');

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
            $hbFile,
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

    public function uploadHubFileAction(ServerRequest $request, Response $response): Response
    {
        $uploadedFiles = $request->getUploadedFiles();

        /** @var UploadedFile $uploadedHubFile */
        $uploadedHubFile = $uploadedFiles['file'];
        $targetFilePath = $this->storageService->getTmpFileUploadAbsolutePath($uploadedHubFile->getClientFilename());

        $uploadedHubFile->moveTo($targetFilePath);

        return $response->withJson(
            [
                'uploaded_file_name' => pathinfo($targetFilePath, PATHINFO_BASENAME),
            ],
            200
        );
    }

    public function listAction(ServerRequest $request, Response $response): Response
    {
        $projectId = $request->getParam('project_id');

        $page = $request->getParam('page', self::DEFAULT_PAGE_VALUE);
        $perPage = $request->getParam('per_page', self::MAX_PER_PAGE);

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
        $jobs = $this->montageJobRepository->getList($projectId, $offset, $perPage);
        $totalCount = $this->montageJobRepository->getTotalCount($projectId);

        $responseData = [];
        foreach ($jobs as $job) {
            $responseData[] = [
                'id' => $job['id'],
                'address_line1' => $job['addressLine1'],
                'address_line2' => $job['addressLine2'],
                'building_type' => $job['buildingType']->value,
                'status' => $job['status']->value,
                'hb_file_path' => $job['hbFilePath'],
                'registration_date' => $job['createdAt']->format('Y-m-d H:i:s'),
                'nvt_code' => $job['nvtCode'],
                'subproject_code' => $job['subprojectCode'],
                'project_name' => $job['projectName'],
                'coworker_name' => $job['coworkerName'],
                'cabel_type' => $job['cabelType'],
                'cabel_code' => $job['cabelCode'],
                'tube_color' => $job['tubeColor'],
                'hup_code' => $job['hupCode'],
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

    public function deleteOneAction(ServerRequest $request, Response $response)
    {
        $jobId = $request->getAttribute('id');
        $montageJob = $this->montageJobRepository->findById($jobId);
        if (is_null($montageJob)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->montageJobService->deleteJob($montageJob);

        return $response->withJson([], 204);
    }

    public function publishOneAction(ServerRequest $request, Response $response): Response
    {
        $jobId = $request->getAttribute('id');
        $montageJob = $this->montageJobRepository->findById($jobId);
        if (is_null($montageJob)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->montageJobService->publishJob($montageJob);

        return $response->withJson([], 204);
    }

    public function unpublishOneAction(ServerRequest $request, Response $response): Response
    {
        $jobId = $request->getAttribute('id');
        $montageJob = $this->montageJobRepository->findById($jobId);
        if (is_null($montageJob)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->montageJobService->unpublishJob($montageJob);

        return $response->withJson([], 204);
    }

    /**
     * @throws \Exception
     */
    public function getOneAction(ServerRequest $request, Response $response): Response
    {
        $jobId = $request->getAttribute('id');
        $montageJob = $this->montageJobRepository->findById($jobId);
        if (is_null($montageJob)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $ontCollection = $montageJob->getOnts()->getIterator();

        $ontData = [];
        /** @var MontageJobOnt $ontItem */
        foreach ($ontCollection as $ontItem) {
            $ontData[] = [
                'id' => $ontItem->getId(),
                'code' => $ontItem->getCode(),
                'splitter_code' => $ontItem->getSplitterCode(),
                'splitter_fiber' => $ontItem->getSplitterFiber(),
                'odf_code' => $ontItem->getOdfCodePlanned(),
                'odf_pos' => $ontItem->getOdfPosPlanned(),
                'is_active' => $ontItem->getActivity() === OntActivity::STATUS_ACTIVE,
                'type' => $ontItem->getType(),
                'customer_name' => $ontItem->getCustomer()?->getName(),
                'customer_email' => $ontItem->getCustomer()?->getEmail(),
                'customer_phone1' => $ontItem->getCustomer()?->getPhoneNumber1(),
                'customer_phone2' => $ontItem->getCustomer()?->getPhoneNumber2(),
            ];
        }

        $details = [
            'id' => $montageJob->getId(),
            'nvt_id' => $montageJob->getNvtId(),
            'subproject_id' => $montageJob->getNvt()->getSubprojectId(),
            'project_id' => $montageJob->getNvt()->getSubproject()->getProjectId(),
            'project_name' => $montageJob->getNvt()->getSubproject()->getProject()->getName(),
            'address_line1' => $montageJob->getAddressLine1(),
            'address_line2' => $montageJob->getAddressLine2(),
            'building_type' => $montageJob->getBuildingType()->value,
            'coworker_id' => $montageJob->getCoworkerId(),
            'coworker_name' => $montageJob->getCoworker()->getCompanyName(),
            'hup_code' => $montageJob->getHup()->getCode(),
            'hup_customer_name' => $montageJob->getHup()->getCustomer()?->getName(),
            'hup_customer_email' => $montageJob->getHup()->getCustomer()?->getEmail(),
            'hup_customer_phone1' => $montageJob->getHup()->getCustomer()?->getPhoneNumber1(),
            'hup_customer_phone2' => $montageJob->getHup()->getCustomer()?->getPhoneNumber2(),
            'cabel_type' => $montageJob->getCabelProperty()->getCabelTypePlanned(),
            'cabel_code' => $montageJob->getCabelProperty()->getCabelCodePlanned(),
            'tube_color' => $montageJob->getCabelProperty()->getTubeColorPlanned(),
            'hb_file_path' => $montageJob->getHbFilePath(),
            'ont' => $ontData,
        ];

        return $response->withJson(['item' => $details], 200);
    }

    /**
     * @throws OptimisticLockException
     * @throws RecordNotFoundException
     * @throws ORMException
     */
    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $jobId = $request->getAttribute('id');
        $montageJob = $this->montageJobRepository->findById($jobId);
        if (is_null($montageJob)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Job with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $nvtId = $request->getParam('nvt_id');
        $addressLine1 = $request->getParam('address_line1');
        $addressLine2 = $request->getParam('address_line2');
        $buildingType = $request->getParam('building_type');
        $coworkerId = $request->getParam('coworker_id');
        $hbFile = $request->getParam('hb_file');

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

        $this->montageJobService->updateJob(
            $montageJob,
            $nvtId,
            $addressLine1,
            $addressLine2,
            BuildingType::from($buildingType),
            $coworkerId,
            $hbFile,
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

        return $response->withJson([], 204);
    }
}