<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;

class CustomJsonMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // Get the data from the response
        $data = json_decode((string)$response->getBody(), true);

        // Modify the data structure
        $modifiedData = $data['data'];

        $streamFactory = new StreamFactory();
        $stream = $streamFactory->createStream(json_encode($modifiedData));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);
    }
}
