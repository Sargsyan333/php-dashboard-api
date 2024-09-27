<?php

namespace Riconas\RiconasApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\Coworker\Service\CoworkerService;
use Riconas\RiconasApi\Components\User\Repository\UserRepository;
use Riconas\RiconasApi\Components\User\UserRole;
use Slim\Http\ServerRequest;

class CoworkerController extends BaseController
{
    private const ERROR_DUPLICATE_COWORKER_NAME = 'duplicate_name';
    private const ERROR_DUPLICATE_COWORKER_EMAIL = 'duplicate_email';
    protected const MAX_PER_PAGE = 20;

    private CoworkerService $coworkerService;

    private CoworkerRepository $coworkerRepository;

    private UserRepository $userRepository;

    public function __construct(
        CoworkerService $coworkerService,
        CoworkerRepository $coworkerRepository,
        UserRepository $userRepository
    ) {
        $this->coworkerService = $coworkerService;
        $this->coworkerRepository = $coworkerRepository;
        $this->userRepository = $userRepository;
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $companyName = $request->getParam('company_name');
        $email = $request->getParam('email');

        if (empty($companyName) || empty($email)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $coworkerWithSameName = $this->coworkerRepository->findByCompanyName($companyName);
        if (false === is_null($coworkerWithSameName)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_COWORKER_NAME,
                'message' => 'Coworker with same name already exists',
            ];

            return $response->withJson($result, 400);
        }

        $coworkerWithSameEmail = $this->userRepository->findByEmailAndRole($email, UserRole::ROLE_COWORKER);
        if (false === is_null($coworkerWithSameEmail)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_COWORKER_EMAIL,
                'message' => 'Coworker with same email already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->coworkerService->createCoworker($companyName, $email);

        return $response->withJson([], 201);
    }

    public function listAction(ServerRequest $request, Response $response): Response
    {
        $page = $request->getParam('page', 0);
        $perPage = $request->getParam('per_page', self::MAX_PER_PAGE);

        $response = $this->validatePagingParams($page, $perPage, $response);
        if (400 === $response->getStatusCode()) {
            return $response;
        }

        $offset = ($page - 1) * $perPage;
        $coworkers = $this->coworkerRepository->getList($offset, $perPage);

        $responseData = [];
        foreach ($coworkers as $coworker) {
            $responseData[] = [
                'id' => $coworker['id'],
                'company_name' => $coworker['companyName'],
                'registration_date' => $coworker['createdAt']->format('Y-m-d H:i:s'),
                'email' => $coworker['email'],
                'status' => $coworker['status']->value,
            ];
        }

        return $response->withJson(['items' => $responseData], 200);
    }
}
