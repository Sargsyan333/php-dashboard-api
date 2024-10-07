<?php

namespace Riconas\RiconasApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Nvt\Repository\NvtRepository;
use Riconas\RiconasApi\Components\Nvt\Service\NvtService;
use Slim\Http\ServerRequest;

class NvtController extends BaseController
{
    private NvtRepository $nvtRepository;

    private NvtService $nvtService;

    public function __construct(NvtRepository $nvtRepository, NvtService $nvtService)
    {
        $this->nvtRepository = $nvtRepository;
        $this->nvtService = $nvtService;
    }

    public function listAction(ServerRequest $request, Response $response): Response
    {
        $projectId = $request->getParam('project_id');
        $subprojectId = $request->getParam('subproject_id');

        $page = $request->getParam('page', self::DEFAULT_PAGE_VALUE);
        $perPage = $request->getParam('per_page', self::MAX_PER_PAGE);

        if (!empty($subprojectId) && false === is_numeric($subprojectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        if (!empty($projectId) && false === is_numeric($projectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $response = $this->validatePagingParams($page, $perPage, $response);
        if (400 === $response->getStatusCode()) {
            return $response;
        }

        $offset = ($page - self::MIN_PAGE_VALUE) * $perPage;
        $nvtItems = $this->nvtRepository->getList($projectId, $subprojectId, $offset, $perPage);

        $responseData = [];
        foreach ($nvtItems as $nvt) {
            $responseData[] = [
                'id' => $nvt['id'],
                'code' => $nvt['code'],
                'registration_date' => $nvt['createdAt']->format('Y-m-d H:i:s'),
                'coworker_name' => $nvt['coworkerName'],
                'coworker_id' => $nvt['coworkerId'],
                'subproject_code' => $nvt['subprojectCode'],
                'subproject_id' => $nvt['subprojectId'],
                'project_name' => $nvt['projectName'],
                'project_id' => $nvt['projectId'],
            ];
        }

        return $response->withJson(['items' => $responseData], 200);
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $code = $request->getParam('code');
        $subprojectId = $request->getParam('subproject_id');
        $coworkerId = $request->getParam('coworker_id');

        if (empty($code) || empty($subprojectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $nvtWithSameCode = $this->nvtRepository->findByCodeAndSubprojectId($code, $subprojectId);
        if (false === is_null($nvtWithSameCode)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_CODE,
                'message' => 'NVT with same code already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->nvtService->createNvt($code, $subprojectId, $coworkerId);

        return $response->withJson([], 201);
    }

    public function updateOneAction(ServerRequest $request, Response $response): Response
    {
        $newCode = $request->getParam('code');
        $newSubprojectId = $request->getParam('subproject_id');
        $newCoworkerId = $request->getParam('coworker_id');

        if (empty($newCode) || empty($newSubprojectId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $nvtId = $request->getAttribute('id');
        $nvt = $this->nvtRepository->findById($nvtId);
        if (is_null($nvt)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'NVT with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        if ($nvt->getCode() !== $newCode) {
            $nvtWithSameCode = $this->nvtRepository->findByCodeAndSubprojectId($newCode, $newSubprojectId);
            if (false === is_null($nvtWithSameCode)) {
                $result = [
                    'code' => self::ERROR_DUPLICATE_CODE,
                    'message' => 'NVT with same code already exists.',
                ];

                return $response->withJson($result, 400);
            }
        }

        $this->nvtService->updateNvt($nvt, $newCode, $newSubprojectId, $newCoworkerId);

        return $response->withJson([], 204);
    }

    public function deleteOneAction(ServerRequest $request, Response $response)
    {
        $nvtId = $request->getAttribute('id');
        $nvt = $this->nvtRepository->findById($nvtId);
        if (is_null($nvt)) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'NVT with supplied id could not be found.',
            ];

            return $response->withJson($result, 404);
        }

        $this->nvtService->deleteNvt($nvt);

        return $response->withJson([], 204);
    }
}
