<?php

namespace Riconas\RiconasApi\Coworker\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Riconas\RiconasApi\Components\UserPreference\Service\UserPreferenceService;
use Riconas\RiconasApi\Components\UserPreference\UserLanguage;
use Slim\Http\ServerRequest;

class UserController extends BaseController
{
    private UserPreferenceService $userPreferenceService;

    public function __construct(UserPreferenceService $userPreferenceService)
    {
        $this->userPreferenceService = $userPreferenceService;
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
}
