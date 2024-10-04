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
        $email = $request->getParam('email');
        $appName = $request->getHeaderLine('App');

        if (empty($email) || empty($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        if (false === $this->validateAppName($appName)) {
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

        $this->passwordResetRequestService->requestPasswordReset($user);

        return $response->withJson([], 202);
    }

    public function resetPasswordAction(ServerRequest $request, Response $response): Response
    {
        $passwordResetCode = $request->getParam('code');
        $newPassword = $request->getParam('new_password');

        $appName = $request->getHeaderLine('App');

        if (empty($passwordResetCode) || empty($newPassword) || empty($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        if (false === $this->validateAppName($appName)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        try {
            $this->passwordResetRequestService->resetUserPassword($passwordResetCode, $newPassword);
        } catch (RecordNotFoundException) {
            $result = [
                'code' => self::ERROR_NOT_FOUND,
                'message' => 'Password reset code not found',
            ];

            return $response->withJson($result, 404);
        }

        return $response->withJson([], 204);
    }
}
