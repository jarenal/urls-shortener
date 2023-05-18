<?php

declare(strict_types=1);

use App\Application\Actions\Api\v1\ShortUrlsAction;
use App\Application\Middleware\ShortUrlMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['data' => 'Hello world!']));
        return $response;
    });

    $app->group('/api/v1', function (Group $group) {
        $group->post('/short-urls', ShortUrlsAction::class)->add(ShortUrlMiddleware::class);
    });
};
