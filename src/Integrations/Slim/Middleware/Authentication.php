<?php

namespace Riconas\RiconasApi\Integrations\Slim\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Riconas\RiconasApi\Authentication\AuthenticationService;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

class Authentication
{
    private const ERROR_UNAUTHENTICATED = 'unauthenticated';

    private array $noAutRoutePatterns = [
        '/auth',
        '/request-password-reset',
        '/reset-password',
    ];

    private AuthenticationService $authenticationService;

    private ContainerInterface $container;

    public function __construct(AuthenticationService $authenticationService, ContainerInterface $container)
    {
        $this->authenticationService = $authenticationService;
        $this->container = $container;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        if (is_null($route)) {
            throw new HttpNotFoundException($request);
        }

        $routePattern = $route->getPattern();
        if (in_array($routePattern, $this->noAutRoutePatterns)) {
            return $handler->handle($request);
        }

        $authHeaderLine = $request->getHeaderLine('Authorization');
        $accessToken = str_replace('Bearer ', '', $authHeaderLine);

        $authenticatedUser = $this->authenticationService->getAuthenticatedUser($accessToken);

        if ($authenticatedUser) {
            // Set authenticated user in container
            $this->container->set('AuthUser', $authenticatedUser);

            return $handler->handle($request);
        } else {
            $response = new \Slim\Psr7\Response(401);
            $response
                ->getBody()
                ->write(
                    json_encode(
                        [
                            'code' => self::ERROR_UNAUTHENTICATED,
                            'message' => 'UnAuthenticated',
                        ]
                    )
                );
            return $response;
        }
    }
}
