<?php

namespace Riconas\RiconasApi\Auth\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Authentication\AuthenticationService;
use Riconas\RiconasApi\Components\User\Repository\UserRepository;
use Slim\Http\ServerRequest;

class AuthenticationController extends BaseController
{
    private const ERROR_INCORRECT_CREDENTIALS = 'incorrect_credentials';

    private UserRepository $userRepository;
    private AuthenticationService $authenticationService;

    public function __construct(UserRepository $userRepository, AuthenticationService $authenticationService)
    {
        $this->userRepository = $userRepository;
        $this->authenticationService = $authenticationService;
    }

    public function authenticateAction(ServerRequest $request, Response $response): Response
    {
        $email = $request->getParam('email');
        $password = $request->getParam('password');
        $appName = $request->getHeaderLine('App');

        if (empty($email) || empty($password) || empty($appName)) {
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
                'code' => self::ERROR_INCORRECT_CREDENTIALS,
                'message' => 'Incorrect email password'
            ];

            return $response->withJson($result, 401);
        }

        if (!$this->authenticationService->verifyUserPassword($user->getPassword(), $password)) {
            $result = [
                'code' => self::ERROR_INCORRECT_CREDENTIALS,
                'message' => 'Incorrect email password'
            ];

            return $response->withJson($result, 401);
        }

        $accessToken = $this->authenticationService->generateAccessToken($user);
        $response = $response->withHeader('Authorization', 'Bearer ' . $accessToken);

        return $response->withJson([], 204);
    }
}
