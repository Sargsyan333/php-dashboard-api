<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\Coworker\Repository\CoworkerRepository;
use Riconas\RiconasApi\Components\User\Service\UserService;
use Riconas\RiconasApi\Components\User\User;
use Riconas\RiconasApi\Components\UserPreference\Service\UserPreferenceService;
use Riconas\RiconasApi\Components\UserPreference\UserLanguage;
use Riconas\RiconasApi\Exceptions\RecordNotFoundException;
use Slim\Http\ServerRequest;

class UserController extends BaseController
{
    private const ERROR_INCORRECT_PASSWORD = 'wrong_password';

    private UserPreferenceService $userPreferenceService;
    private UserService $userService;
    private CoworkerRepository $coworkerRepository;

    public function __construct(
        UserPreferenceService $userPreferenceService,
        UserService $userService,
        CoworkerRepository $coworkerRepository
    ) {
        $this->userPreferenceService = $userPreferenceService;
        $this->userService = $userService;
        $this->coworkerRepository = $coworkerRepository;
    }

    public function setLanguageAction(ServerRequest $request, Response $response): Response
    {
        $language = $request->getParam('language');
        if (!$language || is_null(UserLanguage::tryFrom($language))) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params'
            ];

            return $response->withJson($result, 400);
        }

        $authenticatedUser = $request->getAttribute('AuthUser');

        $this->userPreferenceService->setLanguagePreference($authenticatedUser->getId(), $language);

        return $response->withJson([], 204);
    }

    public function changePasswordAction(ServerRequest $request, Response $response): Response
    {
        $oldPassword = $request->getParam('old_password');
        $newPassword = $request->getParam('new_password');

        if (empty($oldPassword) || empty($newPassword)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid request params',
            ];

            return $response->withJson($result, 400);
        }

        $authenticatedUser = $request->getAttribute('AuthUser');

        $isPasswordChanged = $this->userService->changePassword($authenticatedUser, $oldPassword, $newPassword);
        if (!$isPasswordChanged) {
            $result = [
                'code' => self::ERROR_INCORRECT_PASSWORD,
                'message' => 'Incorrect current password provided.',
            ];

            return $response->withJson($result, 400);
        }

        return $response->withJson([], 204);
    }

    /**
     * @throws RecordNotFoundException
     */
    public function getDetailsAction(ServerRequest $request, Response $response): Response
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = $request->getAttribute('AuthUser');

        $coworker = $this->coworkerRepository->getByUserId($authenticatedUser->getId());

        $responseData = [
            'id' => $coworker->getId(),
            'company_name' => $coworker->getCompanyName(),
        ];

        return $response->withJson($responseData, 200);
    }
}
