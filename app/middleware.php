<?php

declare(strict_types=1);

use App\Application\Middleware\AuthTokenMiddleware;
use App\Application\Middleware\CustomJsonMiddleware;
use App\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(AuthTokenMiddleware::class);
    $app->add(CustomJsonMiddleware::class);
};
