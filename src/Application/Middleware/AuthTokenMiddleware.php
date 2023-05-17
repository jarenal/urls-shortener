<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class AuthTokenMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->getToken($request);

        if (!$this->isValidToken($token)) {
            throw new RuntimeException('Invalid token');
        }
        return $handler->handle($request->withAttribute('token', $token));
    }

    private function getToken(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine('Authorization');
        preg_match('/Bearer\s+(.+)/', $header, $matches);
        return $matches[1] ?? '';
    }

    private function isValidToken(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        return true;
    }
}
