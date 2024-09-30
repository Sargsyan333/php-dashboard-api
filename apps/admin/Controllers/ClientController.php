<?php

namespace Riconas\RiconasApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Client\Repository\ClientRepository;
use Riconas\RiconasApi\Components\Client\Service\ClientService;
use Slim\Http\ServerRequest;

class ClientController extends BaseController
{
    private const ERROR_DUPLICATE_CLIENT_NAME = 'duplicate_name';
    protected const MAX_PER_PAGE = 20;

    private ClientRepository $clientRepository;

    private ClientService $clientService;

    public function __construct(ClientRepository $clientRepository, ClientService $clientService)
    {
        $this->clientRepository = $clientRepository;
        $this->clientService = $clientService;
    }

    public function createOneAction(ServerRequest $request, Response $response): Response
    {
        $name = $request->getParam('name');

        if (empty($name)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $clientWithSameName = $this->clientRepository->findByName($name);
        if (false === is_null($clientWithSameName)) {
            $result = [
                'code' => self::ERROR_DUPLICATE_CLIENT_NAME,
                'message' => 'Client with same name already exists',
            ];

            return $response->withJson($result, 400);
        }

        $this->clientService->createClient($name);

        return $response->withJson([], 201);
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
        $clients = $this->clientRepository->getList($offset, $perPage);

        $responseData = [];
        foreach ($clients as $client) {
            $responseData[] = [
                'id' => $client['id'],
                'name' => $client['name'],
                'registration_date' => $client['createdAt']->format('Y-m-d H:i:s'),
            ];
        }

        return $response->withJson(['items' => $responseData], 200);
    }
}
