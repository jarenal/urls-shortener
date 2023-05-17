<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use RuntimeException;

class ShortUrlMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $params = (array)$request->getParsedBody();
        if (empty($params['url'])) {
            throw new RuntimeException('URL is required');
        }
        if (!filter_var($params['url'], FILTER_VALIDATE_URL)) {
            throw new RuntimeException('URL is invalid');
        }
        return $handler->handle($request);
    }
}
