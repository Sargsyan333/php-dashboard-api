<?php

namespace Riconas\RiconasApi\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class BaseController
{
    protected const DEFAULT_PAGE_VALUE = 0;
    // We have 0 based pagination
    protected const MIN_PAGE_VALUE = 0;
    protected const ERROR_INVALID_REQUEST_PARAMS = 'invalid_request_params';
    protected const ERROR_NOT_FOUND = 'not_found';

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