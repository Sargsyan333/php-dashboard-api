<?php

use Slim\Http\Response;
use Slim\Http\ServerRequest;

global $app;

$app->get('/', function (ServerRequest $request, Response $response) {
    return $response->withJson(['message' => 'Hello World']);
});