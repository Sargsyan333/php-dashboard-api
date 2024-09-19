<?php

namespace Riconas\RiconasApi\Auth\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\PasswordResetRequest\Service\PasswordResetRequestService;
use Riconas\RiconasApi\Components\User\Repository\UserRepository;
use Riconas\RiconasApi\Components\User\UserRole;
use Slim\Http\ServerRequest;

class PasswordResetController extends BaseController
{
    private UserRepository $userRepository;

    private PasswordResetRequestService $passwordResetRequestService;

    public function __construct(UserRepository $userRepository, PasswordResetRequestService $passwordResetRequestService)
    {
        $this->userRepository = $userRepository;
        $this->passwordResetRequestService = $passwordResetRequestService;
    }

    public function requestPasswordResetAction(ServerRequest $request, Response $response): Response
    {
        $email = $request->getParam('email');
        $appName = $request->getHeaderLine('App');

        if (empty($email) || empty($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $userRole = $appName === 'admin' ? UserRole::ROLE_ADMIN : UserRole::ROLE_COWORKER;
        $user = $this->userRepository->findByEmailAndRole($email, $userRole);
        if (is_null($user)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params'
            ];

            return $response->withJson($result, 400);
        }

        $this->passwordResetRequestService->requestPasswordReset($user->getId());

        // TODO send password reset request email

        return $response->withJson([], 202);
    }
}
