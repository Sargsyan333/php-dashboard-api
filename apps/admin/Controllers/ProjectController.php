<?php

namespace Riconas\RiconasApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Project\Repository\ProjectRepository;
use Riconas\RiconasApi\Components\Project\Service\ProjectService;
use Slim\Http\ServerRequest;

class ProjectController extends BaseController
{
    private const ERROR_DUPLICATE_NAME = 'duplicate_name';
    private const ERROR_DUPLICATE_CODE = 'duplicate_code';
    protected const MAX_PER_PAGE = 100;

    private ProjectService $projectService;

    private ProjectRepository $projectRepository;

    public function __construct(
        ProjectService $projectService,
        ProjectRepository $projectRepository
    ) {
        $this->projectService = $projectService;
        $this->projectRepository = $projectRepository;
    }

    public function listAction(ServerRequest $request, Response $response): Response
    {
        $page = $request->getParam('page', 1);
        $perPage = $request->getParam('per_page', self::MAX_PER_PAGE);

        $response = $this->validatePagingParams($page, $perPage, $response);
        if (400 === $response->getStatusCode()) {
            return $response;
        }

        $offset = ($page - 1) * $perPage;
        $projects = $this->projectRepository->getList($offset, $perPage);

        $responseData = [];
        foreach ($projects as $project) {
            $responseData[] = [
                'id' => $project['id'],
                'code' => $project['code'],
                'name' => $project['name'],
                'registration_date' => $project['createdAt']->format('Y-m-d H:i:s'),
                'client' => $project['clientName'],
            ];
        }

        return $response->withJson(['items' => $responseData], 200);
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $name = $request->getParam('name');
        $code = $request->getParam('code');
        $clientId = $request->getParam('client_id');

        if (empty($name) || empty($code) || empty($clientId)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $projectWithSameName = $this->projectRepository->findByName($name);
        if (false === is_null($projectWithSameName)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_NAME,
                'message' => 'Project with same name already exists',
            ];

            return $response->withJson($result, 400);
        }

        $projectWithSameCode = $this->projectRepository->findByCode($code);
        if (false === is_null($projectWithSameCode)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_CODE,
                'message' => 'Project with same code already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->projectService->createProject($name, $code, $clientId);

        return $response->withJson([], 201);
    }
}