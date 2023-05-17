<?php

declare(strict_types=1);

namespace App\Application\Actions\Api\v1;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;

class ShortUrlsAction extends Action
{
    protected function action(): ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();
        return $this->respondWithData(['url' => $parsedBody['url']]);
    }
}
