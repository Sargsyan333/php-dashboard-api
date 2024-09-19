<?php

namespace Riconas\RiconasApi\Auth\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Authentication\AuthenticationService;
use Riconas\RiconasApi\Components\User\Repository\UserRepository;
use Riconas\RiconasApi\Components\User\UserRole;
use Slim\Http\ServerRequest;

class AuthenticationController extends BaseController
{
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

        if (empty($email) || empty($password)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid username/password'
            ];

            return $response->withJson($result, 400);
        }

        $user = $this->userRepository->findByEmailAndRole($email, UserRole::ROLE_ADMIN);
        if (is_null($user)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Incorrect email password'
            ];

            return $response->withJson($result, 401);
        }

        if (!$this->authenticationService->verifyUserPassword($user->getPassword(), $password)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Incorrect email password'
            ];

            return $response->withJson($result, 401);
        }

        $accessToken = $this->authenticationService->createAccessToken($user);

        return $response->withJson(
            [
                'access_token' => $accessToken,
            ],
            200
        );
    }
}
