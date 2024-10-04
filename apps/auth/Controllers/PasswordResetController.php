<?php

namespace Riconas\RiconasApi\Auth\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\PasswordResetRequest\Service\PasswordResetRequestService;
use Riconas\RiconasApi\Components\User\Repository\UserRepository;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;

class PasswordResetController extends BaseController
{
    private UserRepository $userRepository;
    private PasswordResetRequestService $passwordResetRequestService;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetRequestService $passwordResetRequestService,
    ) {
        $this->userRepository = $userRepository;
        $this->passwordResetRequestService = $passwordResetRequestService;
    }

    public function requestPasswordResetAction(ServerRequest $request, Response $response): Response
    {
        $appName = $this->getAppHeaderValue($request);

        $email = $request->getParam('email');

        if (empty($email) || empty($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        if (false === $this->validateAppHeader($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $userRole = self::APP_NAME_USER_ROLE_MAP[$appName];
        $user = $this->userRepository->findByEmailAndRole($email, $userRole);
        if (is_null($user)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $this->passwordResetRequestService->requestPasswordReset($user, $appName);

        return $response->withJson([], 202);
    }
}
