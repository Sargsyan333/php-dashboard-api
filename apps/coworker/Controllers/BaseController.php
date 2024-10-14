<?php

namespace Riconas\RiconasApi\Coworker\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class BaseController
{
    protected const ERROR_INVALID_REQUEST_PARAMS = 'invalid_request_params';
    protected const DEFAULT_PAGE_NUMBER = 0;
    protected const MIN_PAGE_VALUE = 0;
    protected const MAX_PER_PAGE = 100;
    protected const DEFAULT_PER_PAGE = 10;

    protected function validatePagingParams(string $page, string $perPage, Response $response): Response
    {
        if (!is_numeric($page) || !is_numeric($perPage)) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid paging params'
            ];

            return $response->withJson($result, 400);
        }

        if ($page < self::MIN_PAGE_VALUE || $perPage > static::MAX_PER_PAGE) {
            $result = [
                'code' => self::ERROR_INVALID_REQUEST_PARAMS,
                'message' => 'Invalid paging params'
            ];

            return $response->withJson($result, 400);
        }

        return $response;
    }
}